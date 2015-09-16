<?php
namespace AppBundle\Tests\Entity;

use AppBundle\Entity\RandomHeadTailsGenerator;
use AppBundle\Entity\Game;

class RandomHeadTailsGeneratorTest extends \PHPUnit_Framework_TestCase{
	
	protected $generator;
	
	public function setUp(){
		$this->generator = new RandomHeadTailsGenerator();
	}
	
	/*
	 * It's tough to test this method since it returns random results
	 * This test gets random flips for twenty rolls to make sure the method actually returns heads and tails fliptypes
	 * The chances of not getting at least one head or tail are very slim, so as long as the method works we should get both flip types in our results 
	 */
	public function testDoRandomFlipIsReturningRandomResults(){
		$flips = [];
		for($i = 0; $i < 20; $i++){
			$flips[] = $this->generator->doRandomFlip();
		}
		// make sure method returns heads sometimes
		$this->assertTrue(in_array(Game::FLIP_TYPE_HEADS, $flips));
		// make sure method returns tails sometimes;
		$this->assertTrue(in_array(Game::FLIP_TYPE_TAILS, $flips));
	}

}

