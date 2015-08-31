<?php
namespace AppBundle\Tests\Entity;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use AppBundle\Entity\Game;
use AppBundle\Entity\User;

class GameRepositoryTest extends WebTestCase{
	
	/**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;
    protected $repository;

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
        $this->repository = $this->em->getRepository('AppBundle:Game');
    }

    public function testGetFinishedGamesThatUserHasNotViewedHasGamesForUserWithUnviewedGames()
    {
        $user = new User();
		$user->setId(1);
    	$games = $this->repository->getFinishedGamesThatUserHasNotViewed($user);
        $this->assertCount(1, $games);
        $this->assertInstanceOf('AppBundle\Entity\Game', $games[0]);
    }
    
    public function testGetFinishedGamesThatUserHasNotViewedIsEmptyForUserWithZeroUnviewedGames()
    {
    	$user = new User();
    	$user->setId(2);
    	$games = $this->repository->getFinishedGamesThatUserHasNotViewed($user);
    	$this->assertCount(0, $games);
    }
    
    public function testGetUserLiveGamesForUserWithLiveGames()
    {
    	$user = new User();
    	$user->setId(1);
    	$games = $this->repository->getUserLiveGames($user);
    	$this->assertCount(2, $games);
    	$this->assertInstanceOf('AppBundle\Entity\Game', $games[0]);
    }
    
    public function testGetUserLiveGamesForUserWithoutLiveGames()
    {
    	$user = new User();
    	$user->setId(2);
    	$games = $this->repository->getUserLiveGames($user);
    	$this->assertCount(0, $games);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

}
