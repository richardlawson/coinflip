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
		
		$manager->persist($game);
		$manager->persist($game2);
		$manager->flush();
	
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getOrder()
	{
		return 2; // the order in which fixtures will be loaded
	}
}