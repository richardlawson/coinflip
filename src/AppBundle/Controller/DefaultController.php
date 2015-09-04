<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
    	if($this->get('security.context')->isGranted('ROLE_USER')){
    		return $this->redirectToRoute('game_home');
    	}else{
    		return $this->render('default/index.html.twig', array());
    	}
        
    }
}
