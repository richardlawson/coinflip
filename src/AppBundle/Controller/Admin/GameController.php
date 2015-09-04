<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Game;
use AppBundle\Entity\Player;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\Type\GameType;
use AppBundle\Entity\RandomHeadTailsGenerator;
use AppBundle\Socket\GameUpdateNotifier;

class GameController extends Controller
{
	
    /**
     * @Route("/admin", name="admin_game_home")
     */
    public function indexAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$games = $em->getRepository('AppBundle:Game')->getAllLiveGames();
 
        return $this->render('admin/game/index.html.twig', array(
            'games' => $games,
        ));
       
    }
    
    /**
     * @Route("/admin/game/add", name="admin_game_add")
     */
    public function addAction(Request $request)
    {
    	$game = new Game(new RandomHeadTailsGenerator());
    	$form = $this->createForm(new GameType(), $game);
    	
    	$form->handleRequest($request);
    	
    	if ($form->isSubmitted() && $form->isValid()) {
    		$em = $this->getDoctrine()->getManager();
    		$em->persist($game);
    		$em->flush();
    		$this->notifyPlayersOfGameUpdate($game);
    		return $this->redirectToRoute('admin_game_home');
    	}
    	
    	return $this->render('admin/game/add.html.twig', array(
    		'game' => $game,
    		'form' => $form->createView(),
    	));
    	 
    }
    
    protected function notifyPlayersOfGameUpdate(Game $game)
    {
    	$updateNotifier = new GameUpdateNotifier($this->container->getParameter('zmq_server_address'),$game);
    	$updateNotifier->notifySocketServer();
    }
    
    /**
     * @Route("/admin/game/edit/{id}", requirements={"id" = "\d+"}, name="admin_game_edit")
     */
    public function editAction(Game $game, Request $request)
    {
    	$game->setOldName($game->getName());
    	$form = $this->createForm(new GameType(), $game);
    	 
    	$form->handleRequest($request);
    	 
    	if ($form->isSubmitted() && $form->isValid()) {
    		$em = $this->getDoctrine()->getManager();
    		$em->flush();
    		$this->notifyPlayersOfGameUpdate($game);
    		return $this->redirectToRoute('admin_game_home');
    	}
    	 
    	return $this->render('admin/game/edit.html.twig', array(
    		'game' => $game,
    		'form' => $form->createView(),
    	));
    
    }
    
    /**
     * @Route("/admin/game/delete/{id}", requirements={"id" = "\d+"}, name="admin_game_delete")
     */
    public function deleteAction(Game $game)
    {
    	if($game->getPlayerCount() > 0){
    		$this->addFlash('notice', 'You can\'t delete a game with players');
    	}else{
    		$em = $this->getDoctrine()->getManager();
    		$em->remove($game);
    		$em->flush();
    	}
    	return $this->redirectToRoute('admin_game_home');
    }
    
}
