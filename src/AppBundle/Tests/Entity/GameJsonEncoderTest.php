<?php
namespace AppBundle\Tests\Entity;

use AppBundle\Entity\GameJsonEncoder;
use AppBundle\Entity\Game;
use AppBundle\Entity\User;
use AppBundle\Entity\Player;
use AppBundle\Entity\RandomHeadTailsGenerator;

class GameJsonEncoderTest extends \PHPUnit_Framework_TestCase{

	public function setUp(){
		
	}
	
	public function testGetEncodedObject(){
		$expectedJson = '{"id":null,"name":"Arizona","noPlayers":2,"state":2}';
		$game = $this->setUpGame();
		$jsonEncoder = new GameJsonEncoder($game);
		$gameJson = $jsonEncoder->getLiteObject();
		$this->assertEquals($expectedJson, $gameJson);
	}
	
	protected function setUpGame(){
		$game = new Game(new RandomHeadTailsGenerator());
		$game->setName('Arizona');
		$user = new User();
		$user->setId(1);
		$user->setUsername('ricardo75');
		$player = new Player($user, Game::FLIP_TYPE_HEADS);
		$game->addPlayer($player);
		
		$user2 = new User();
		$user->setId(2);
		$user2->setUsername('flipshark');
		$player2 = new Player($user2, Game::FLIP_TYPE_TAILS);
		$game->addPlayer($player2);
		
		return $game;
	}

}
