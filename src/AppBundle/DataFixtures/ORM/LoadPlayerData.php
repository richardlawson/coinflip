<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;
use AppBundle\Entity\Player;
use AppBundle\Entity\Game;
use AppBundle\Entity\RandomHeadTailsGenerator;

class LoadPlayerData extends AbstractFixture implements OrderedFixtureInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		$this->setUpOnePlayerGame($manager);
		$this->setUpTwoPlayerGameWhereOnlyOnePlayerHasViewedTheGame($manager);
	}
	
	protected function setUpOnePlayerGame($manager){
		$player = new Player($this->getReference('user-ricardo75'), Game::FLIP_TYPE_HEADS);
		$player->setGame($this->getReference('game-new-york'));
		$manager->persist($player);
		$manager->flush();
	}
	
	protected function setUpTwoPlayerGameWhereOnlyOnePlayerHasViewedTheGame($manager){
		$game = $this->getReference('game-nevada');
		$player1 = new Player($this->getReference('user-ricardo75'), Game::FLIP_TYPE_HEADS);
		$player1->setGame($game);
		$manager->persist($player1);
		
		$player2 = new Player($this->getReference('user-flipshark'), Game::FLIP_TYPE_TAILS);
		$player2->setGame($game);
		$player2->setViewedGame(true);
		$manager->persist($player2);
		
		$game->addPlayer($player1);
		$game->addPlayer($player2);
		$game->setRandomGenerator(new RandomHeadTailsGenerator());
		$game->playGame();
		
		$manager->flush();
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getOrder()
	{
		return 3; // the order in which fixtures will be loaded
	}
}