<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Game;
use AppBundle\Entity\RandomHeadTailsGenerator;

class LoadGameData extends AbstractFixture implements OrderedFixtureInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		$game = new Game(new RandomHeadTailsGenerator());
		$game->setName('Arizona');
		
		$game2 = new Game(new RandomHeadTailsGenerator());
		$game2->setName('Detroit');
		
		$game3 = new Game(new RandomHeadTailsGenerator());
		$game3->setName('California');
		
		$game4 = new Game(new RandomHeadTailsGenerator());
		$game4->setName('Ohio');
		
		$game5 = new Game(new RandomHeadTailsGenerator());
		$game5->setName('New York');
		
		$game6 = new Game(new RandomHeadTailsGenerator());
		$game6->setName('Nevada');
		
		$game7 = new Game(new RandomHeadTailsGenerator());
		$game7->setName('Utah');
		
		$manager->persist($game);
		$manager->persist($game2);
		$manager->persist($game3);
		$manager->persist($game4);
		$manager->persist($game5);
		$manager->persist($game6);
		$manager->persist($game7);
		$manager->flush();
		
		$this->addReference('game-new-york', $game5);
		$this->addReference('game-nevada', $game6);
		$this->addReference('game-utah', $game7);
	
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getOrder()
	{
		return 2; // the order in which fixtures will be loaded
	}
}