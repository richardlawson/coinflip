<?php
namespace AppBundle\Tests\Entity;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use AppBundle\Entity\Game;
use AppBundle\Entity\RandomHeadTailsGenerator;
use AppBundle\Entity\User;
use AppBundle\Entity\Player;

class GameIntegrationTest extends WebTestCase{
	
	/**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {   
        $classes = array(
        	'AppBundle\DataFixtures\ORM\LoadUserData',
        	'AppBundle\DataFixtures\ORM\LoadGameData',
        	'AppBundle\DataFixtures\ORM\LoadPlayerData',
        );
        $this->loadFixtures($classes);
        $kernel = static::createKernel();
        $kernel->boot();
        $this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testThatCreatedAtIsCreatedWhenAnNewGameIsSaved()
    {
    	$game = new Game(new RandomHeadTailsGenerator());
    	$game->setName('Wyoming');
    	$this->assertNull($game->getCreatedAt());
    	$this->em->persist($game);
    	$this->assertInstanceOf('\DateTime', $game->getCreatedAt());
    }
    
    public function testThatFinishedAtIsCreatedWhenANewlyFinishedGameIsUpdated()
    {
    	$game = new Game(new RandomHeadTailsGenerator());
    	$game->setName('Wyoming');
    	$this->em->persist($game);
    	$this->em->flush();
    	$this->assertNull($game->getFinishedAt());
    	$this->setUpTwoPlayerGame($game);
    	$this->assertInstanceOf('\DateTime', $game->getFinishedAt());
    }
    
    protected function setUpTwoPlayerGame($game){
    	$user1 = $this->em->getRepository('AppBundle:User')->find(1);
    	
    	$player1 = new Player($user1, Game::FLIP_TYPE_HEADS);
    	$player1->setGame($game);
    	$this->em->persist($player1);
    
    	$user2 = $this->em->getRepository('AppBundle:User')->find(2);
    	
    	$player2 = new Player($user2, Game::FLIP_TYPE_TAILS);
    	$player2->setGame($game);
    	$this->em->persist($player2);
    
    	$game->setRandomGenerator(new RandomHeadTailsGenerator());
    	$game->playGame();
    
    	$this->em->flush();
    }
    
   
}
