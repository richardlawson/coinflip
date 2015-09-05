<?php
namespace AppBundle\Tests\Entity;

use AppBundle\Entity\PlayerJsonEncoder;
use AppBundle\Entity\Game;
use AppBundle\Entity\User;
use AppBundle\Entity\Player;

class PlayerJsonEncoderTest extends \PHPUnit_Framework_TestCase{

	public function setUp(){
		
	}
	
	public function testGetEncodedObject(){
		$expectedJson = '{"id":null,"username":"ricardo75","flipType":1,"flipTypeAsString":"heads"}';
		$player = $this->setUpPlayer();
		$jsonEncoder = new PlayerJsonEncoder($player);
		$playerJson = $jsonEncoder->getLiteObject();
		$this->assertEquals($expectedJson, $playerJson);
	}
	
	protected function setUpPlayer(){
		$user = new User();
		$user->setId(1);
		$user->setUsername('ricardo75');
		$player = new Player($user, Game::FLIP_TYPE_HEADS);
		return $player;
	}

}
