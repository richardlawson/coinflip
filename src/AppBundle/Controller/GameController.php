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

class GameController extends Controller
{
	const REFRESH_RATE_SECS = 30;
	
    /**
     * @Route("/secure/games", name="game_home")
     */
    public function indexAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$games = $em->getRepository('AppBundle:Game')->findAll();
    	$unviewedGames = $em->getRepository('AppBundle:Game')->getFinishedGamesThatUserHasNotViewed($this->getUser());
    	$liveGames = $em->getRepository('AppBundle:Game')->getUserLiveGames($this->getUser());
        return $this->render('game/index.html.twig', array(
            'games' => $games,
        	'unviewedGames'	=> $unviewedGames,
            'liveGames'	=> $liveGames,
        ));
    }
    
    protected function setUpPageRefresh(){
    	$response = new Response();
    	$response->headers->set('Refresh', self::REFRESH_RATE_SECS);
    	$response->send();
    }
    
    /**
     * @Route("/secure/game/{id}", requirements={"id" = "\d+"}, name="game_view")
     */
    public function viewAction(Game $game, Request $request)
    {
    	$headsForm = $this->getHeadsFlipForm();
    	$tailsForm = $this->getTailsFlipForm();
    	$user = $this->getUser();
    	
    	if($request->isMethod('POST')){
    		$flipType = $this->getFlipTypeFromForm($headsForm, $tailsForm, $request);
    		$em = $this->getDoctrine()->getManager();
    		$this->createPlayerAddToGameAndPersist($user, $game, $flipType, $em);
    		if($game->isGameReady()){
    			$game->setRandomGenerator(new RandomHeadTailsGenerator());
    			$game->playGame();
    			$em->flush();
    			$this->notifyPlayersOfGameUpdate($game);
    			return $this->redirectToRoute('game_play', array('id' => $game->getId()), 301);
    		}
    		$em->flush();
    		$this->notifyPlayersOfGameUpdate($game);
    	}
    	
    	$userInGame = $game->isUserInGame($user);
    	$gameFinished = $game->getGameState() == Game::STATE_FINISHED;
    	
    	return $this->render('game/view.html.twig', array(
    		'game' => $game,
    		'userInGame' => $game->isUserInGame($user),
    		'formHeads' => $headsForm->createView(),
    		'formTails' => $tailsForm->createView(),
    		'gameFinished' => $gameFinished
    	));
    }
    
    protected function getHeadsFlipForm(){
    	$defaultData = array('flipType' => Game::FLIP_TYPE_HEADS);
    	$form = $this->get('form.factory')->createNamedBuilder('formheads', 'form', $defaultData)
    	->add('flipType', 'text')
    	->add('heads', 'submit')
    	->getForm();
    	return $form;
    }
    
    protected function getTailsFlipForm(){
    	$defaultData = array('flipType' => Game::FLIP_TYPE_TAILS);
    	$form =$this->get('form.factory')->createNamedBuilder('formtails', 'form', $defaultData)
    	->add('flipType', 'text')
    	->add('tails', 'submit')
    	->getForm();
    	return $form;
    }
    
	protected function getFlipTypeFromForm($headsForm, $tailsForm, Request $request){
		$form = $this->getSubmittedForm($headsForm, $tailsForm, $request);
		$form->bind($request);
		$data = $form->getData();
		return (int) $data['flipType'];
	}
	
	/*
	 * returns either headsform or tails form depending on which one user submitted
	 */
	protected function getSubmittedForm($headsForm, $tailsForm, Request $request){
		$form = $headsForm;
		if($request->request->has('formtails')){
			$form = $tailsForm;
		} 
		return $form;
	}
	
	protected function createPlayerAddToGameAndPersist(User $user, Game $game, $flipType, EntityManager $em){
		$player = new Player($user, $flipType);
		// this method sets the players game and also adds the player to the game
    	$player->setGame($game);
    	$em->persist($player);
	}
	
	protected function notifyPlayersOfGameUpdate(Game $game){
		$encoder = new GameJsonEncoder($game);
		$jsonGame = $encoder->getLiteObject();
		$context = new \ZMQContext();
		$socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'game pusher');
		$socket->connect("tcp://localhost:5555");
		$socket->send($jsonGame);
	}
	
	 /**
     * @Route("/secure/game-play/{id}", requirements={"id" = "\d+"}, name="game_play")
     */
    public function playAction(Game $game)
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
    	
    	return $this->render('game/play.html.twig', array(
    		'game' => $game,
    	));
    }

}
