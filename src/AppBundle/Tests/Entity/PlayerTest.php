<?php
namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Game;
use AppBundle\Entity\User;
use AppBundle\Entity\Player;

class PlayerTest extends \PHPUnit_Framework_TestCase{
	
	protected $invalidFlipType;
	protected $user2;
	
	public function setUp(){
		$this->user1 = new User();
		$this->user1->setUsername('ricardo75');
		$this->user2 = new User();
		$this->user2->setUsername('coolcat');
	}
	
	public function testPlayerObjectCreatedWhenUsingFlipTypeHeads(){
		$player1 = new Player($this->user1, Game::FLIP_TYPE_HEADS);
		$this->assertInstanceOf('AppBundle\Entity\Player', $player1);
	}
	
	public function testPlayerObjectCreatedWhenUsingFlipTypeTails(){
		$player1 = new Player($this->user1, Game::FLIP_TYPE_TAILS);
		$this->assertInstanceOf('AppBundle\Entity\Player', $player1);
	}
	
	/**
	 * @expectedException AppBundle\Entity\InvalidFlipTypeException
	 */
	public function testExceptionThrownWhenFlipTypeNotVaild(){
		$invalidFlipType = 3;
		$player1 = new Player($this->user1, $invalidFlipType);
	}

}
