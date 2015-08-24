<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Game;

class GameController extends Controller
{
    /**
     * @Route("/games", name="gamehome")
     */
    public function indexAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$games = $em->getRepository('AppBundle:Game')->findAll();
        return $this->render('game/index.html.twig', array(
            'games' => $games,
        ));
    }
    
    /**
     * @Route("/game/{id}", requirements={"id" = "\d+"}, name="viewgame")
     */
    public function viewAction(Game $game)
    {
    	return $this->render('game/view.html.twig', array(
    		'game' => $game,
    	));
    }
}
