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
    
    public function testRemoveUserFromGame()
    {
    	$user = new User();
    	$user->setId(1);
    	$this->assertInstanceOf('AppBundle\Entity\Player', $this->em->getRepository('AppBundle:Player')->find(2));
    	$game = $this->em->getRepository('AppBundle:Game')->find(6);
    	$this->em->getRepository('AppBundle:Game')->removeUserFromGame($user, $game);
    	$this->assertEquals(Game::STATE_INITIALIZED, $game->getGameState());
    	$this->assertNull($this->em->getRepository('AppBundle:Player')->find(2));
    }
    
    public function testGetAllLiveGames()
    {
    	$games = $this->repository->getAllLiveGames();
    	$this->assertCount(7, $games);
    	$this->assertInstanceOf('AppBundle\Entity\Game', $games[0]);
    }
    
    public function testGetGameNamesInUse()
    {
    	$names = $this->repository->getGameNamesInUse();
    	$this->assertCount(7, $names);
    }
    
    public function testIsNameInUseWhenLiveGameExistsWithName()
    {
    	$newGameName = 'Arizona';
    	$this->assertTrue($this->repository->isNameInUse($newGameName));
    }
    
    public function testIsNameInUseWhenNoLiveGamesExistWithName()
    {
    	$newGameName = 'New Hampshire';
    	$this->assertFalse($this->repository->isNameInUse($newGameName));
    }
    
    public function testIsNameInUseReturnsFalseWhenFinishedGamesExistWithNameButNoLiveGames()
    {
    	$newGameName = 'Utah';
    	$this->assertFalse($this->repository->isNameInUse($newGameName));
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
