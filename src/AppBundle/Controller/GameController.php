<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Game;
use AppBundle\Entity\Player;
use AppBundle\Entity\User;
use AppBundle\Entity\GameJsonEncoder;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\RandomHeadTailsGenerator;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Socket\GameUpdateNotifier;
use AppBundle\Form\Type\RemovePlayerType;
use AppBundle\Form\Type\HeadsFlipType;
use AppBundle\Form\Type\TailsFlipType;
use AppBundle\Entity\GameManager;
use AppBundle\Entity\GameNameFinder;

class GameController extends Controller
{
	const REFRESH_RATE_SECS = 20;
	
    /**
     * @Route("/secure/games", name="game_home")
     */
    public function indexAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$games = $em->getRepository('AppBundle:Game')->getAllLiveGames();
    	$unviewedGames = $em->getRepository('AppBundle:Game')->getFinishedGamesThatUserHasNotViewed($this->getUser());
    	$liveGames = $em->getRepository('AppBundle:Game')->getUserLiveGames($this->getUser());
        
        $content = $this->renderView('game/index.html.twig', array(
            'games' => $games,
        	'unviewedGames'	=> $unviewedGames,
            'liveGames'	=> $liveGames,
        ));
        
        $response = new Response($content);
        $this->disablePageCaching($response);
        
        $socketPinger = $this->get('socket_pinger');
        if(!$socketPinger->isSocketServerWorking()){
        	$this->setUpPageRefresh($response);
        }
    	
        return $response;
    }
    
    protected function setUpPageRefresh(Response $response)
    {
    	$response->headers->set('Refresh', self::REFRESH_RATE_SECS);
    	$response->send();
    }
    
    protected function disablePageCaching(Response $response){
	    $response->headers->addCacheControlDirective('no-cache', true);
	    $response->headers->addCacheControlDirective('max-age', 0);
	    $response->headers->addCacheControlDirective('must-revalidate', true);
	    $response->headers->addCacheControlDirective('no-store', true);
    }
    
    public function userLiveGamesAction(){
    	$em = $this->getDoctrine()->getManager();
    	$liveGames = $em->getRepository('AppBundle:Game')->getUserLiveGames($this->getUser());
    	return $this->render('game/user_live_games.html.twig', array(
    		'liveGames' => $liveGames,
    	));
    }
    
    /**
     * @Route("/secure/game/{id}", requirements={"id" = "\d+"}, name="game_view")
     */
    public function viewAction(Game $game, Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
    	$headsForm = $this->createForm(new HeadsFlipType(), array('flipType' => Game::FLIP_TYPE_HEADS));
    	$tailsForm = $this->createForm(new TailsFlipType(), array('flipType' => Game::FLIP_TYPE_TAILS));
    	$removeForm = $this->createForm(new RemovePlayerType(), array(), array(
    		'action' => $this->generateUrl('game_remove_player', array('id' => $game->getId())),
		));
		
    	$user = $this->getUser();
    	
    	if($request->isMethod('POST')){
    		$flipType = $this->getFlipTypeFromForm($headsForm, $tailsForm, $request);
    		$this->createPlayerAddToGameAndPersist($user, $game, $flipType, $em);
    		if($game->isGameReady()){
    			$this->playGame($game, $em);
    			$replacementGame = $this->createReplacementGame($game, $em); 
    			$this->notifyPlayersOfGameUpdate($replacementGame);
    			$this->notifyPlayersOfGameUpdate($game);
    			return $this->redirectToRoute('game_play', array('id' => $game->getId()), 301);
    		}
    		$em->flush();
    		$this->notifyPlayersOfGameUpdate($game);
    	}
    	
    	$userInGame = $game->isUserInGame($user);
    	$gameFinished = $game->getGameState() == Game::STATE_FINISHED;
    	$liveGames = $em->getRepository('AppBundle:Game')->getUserLiveGames($this->getUser());
    	
    	return $this->render('game/view.html.twig', array(
    		'game' => $game,
    		'liveGames' => $liveGames,
    		'userInGame' => $game->isUserInGame($user),
    		'formHeads' => $headsForm->createView(),
    		'formTails' => $tailsForm->createView(),
    		'formRemove' => $removeForm->createView(),
    		'gameFinished' => $gameFinished
    	));
    }
    
	protected function getFlipTypeFromForm($headsForm, $tailsForm, Request $request)
	{
		$form = $this->getSubmittedForm($headsForm, $tailsForm, $request);
		$form->bind($request);
		$data = $form->getData();
		return (int) $data['flipType'];
	}
	
	/*
	 * returns either headsform or tails form depending on which one user submitted
	 */
	protected function getSubmittedForm($headsForm, $tailsForm, Request $request)
	{
		$form = $headsForm;
		if($request->request->has('tailsFlip')){
			$form = $tailsForm;
		}
		return $form;
	}
	
	protected function playGame(Game $game, EntityManager $em)
	{
		$game->setRandomGenerator(new RandomHeadTailsGenerator());
    	$game->playGame();
    	$em->flush();
	}
	
	protected function createReplacementGame(Game $game, EntityManager $em)
	{
		$gameManager = new GameManager();
		$gameNameFinder = new GameNameFinder($em->getRepository('AppBundle:Game')->getGameNamesInUse());
		$replacementGame = $gameManager->getReplacementGame($game, $gameNameFinder);
		$em->persist($replacementGame);
		$em->flush();
		return $replacementGame;
	}
	
	protected function createPlayerAddToGameAndPersist(User $user, Game $game, $flipType, EntityManager $em)
	{
		$player = new Player($user, $flipType);
		// this method sets the players game and also adds the player to the game
    	$player->setGame($game);
    	$em->persist($player);
	}
	
	protected function notifyPlayersOfGameUpdate(Game $game)
	{
		$updateNotifier = new GameUpdateNotifier($this->container->getParameter('zmq_server_address'),$game);
		$updateNotifier->notifySocketServer();
	}
	
	 /**
     * @Route("/secure/game-play/{id}", requirements={"id" = "\d+"}, name="game_play")
     */
    public function playAction(Game $game, Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
    	$user = $this->getUser();
    	//make sure only game players can see this page
    	if(!$game->isUserInGame($user)){
    		return $this->redirectToRoute('game_home');
    	}
    	
    	$player = $game->getPlayerByUserId($user->getId());
    	$player->setViewedGame(true);
    	$em->flush();
    	
    	$template = 'game/play.html.twig';
    	$isPopup = $request->query->get('popup', false);
    	if($isPopup){
    		$template = 'game/play_popup.html.twig';
    	}
    	
    	return $this->render($template, array(
    		'game' => $game,
    	));
    }
    
    /**
     * @Route("/secure/game-remove-player/{id}", requirements={"id" = "\d+"}, name="game_remove_player")
     */
    public function removePlayerAction(Game $game, Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
    	//make sure only game players can remove themselves
    	$user = $this->getUser();
    	if(!$game->isUserInGame($user)){
    		return $this->redirectToRoute('game_home');
    	}
    	if($request->isMethod('POST')){
    		$em->getRepository('AppBundle:Game')->removeUserFromGame($user, $game);
    		$this->notifyPlayersOfGameUpdate($game);
    		$this->addFlash('notice', 'You have been removed from this game');
    		return $this->redirectToRoute('game_view', array('id' => $game->getId()), 301);
    	}
    	// we should never get to this point
    }
}
