<?php
namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Game;
use AppBundle\Entity\User;
use AppBundle\Entity\Player;
use AppBundle\Entity\FlipTypeAlreadySelectedException;
use AppBundle\Entity\RandomHeadTailsGenerator;
use AppBundle\Entity\NotEnoughPlayersException;

class GameTest extends \PHPUnit_Framework_TestCase{
	
	protected $game;
	protected $user1;
	protected $user2;
	
	public function setUp(){
		$this->game = new Game(new RandomHeadTailsGenerator());
		$this->user1 = new User();
		$this->user1->setUsername('ricardo75');
		$this->user2 = new User();
		$this->user2->setUsername('coolcat');
	}
	
	public function testGameObjectCreated(){
		$this->assertInstanceOf('AppBundle\Entity\Game', $this->game);
	}
	
	public function testPlayerCountReturnsOneWhenOnePlayerAdded(){
		$expected = 1;
		$player1 = new Player($this->user1, Game::FLIP_TYPE_HEADS);
		$this->game->addPlayer($player1);
		$this->assertEquals($expected, $this->game->getPlayerCount());
	}
	
	public function testPlayerCountReturnsTwoWhenTwoPlayersAdded(){
		$expected = 2;
		$player1 = new Player($this->user1, Game::FLIP_TYPE_HEADS);
		$this->game->addPlayer($player1);
		$player2 = new Player($this->user2, Game::FLIP_TYPE_TAILS);
		$this->game->addPlayer($player2);
		$this->assertEquals($expected, $this->game->getPlayerCount());
	}
	
	/**
	 * @expectedException AppBundle\Entity\FlipTypeAlreadySelectedException
	 */
	public function testCantAddTwoPlayersWithSameFlipTypeOfHeads(){
		$player1 = new Player($this->user1, Game::FLIP_TYPE_HEADS);
		$this->game->addPlayer($player1);
		$player2 = new Player($this->user2, Game::FLIP_TYPE_HEADS);
		$this->game->addPlayer($player2);
	}
	
	/**
	 * @expectedException AppBundle\Entity\FlipTypeAlreadySelectedException
	 */
	public function testCantAddTwoPlayersWithSameFlipTypeOfTails(){
		$player1 = new Player($this->user1, Game::FLIP_TYPE_TAILS);
		$this->game->addPlayer($player1);
		$player2 = new Player($this->user2, Game::FLIP_TYPE_TAILS);
		$this->game->addPlayer($player2);
	}
	
	public function testIsGameReadyFalseWhenOnlyOnePlayer(){
		$player1 = new Player($this->user1, Game::FLIP_TYPE_TAILS);
		$this->game->addPlayer($player1);
		$this->assertFalse($this->game->isGameReady());
	}
	
	public function testIsGameReadyWhenTwoPlayerReadyToPlay(){
		$player1 = new Player($this->user1, Game::FLIP_TYPE_TAILS);
		$this->game->addPlayer($player1);
		$player2 = new Player($this->user2, Game::FLIP_TYPE_HEADS);
		$this->game->addPlayer($player2);
		$this->assertTrue($this->game->isGameReady());
	}
	
	public function testPlayGameReturnsPlayerWithHeadsWhenHeadsWins(){
		$player1 = new Player($this->user1, Game::FLIP_TYPE_TAILS);
		$this->game->addPlayer($player1);
		$player2 = new Player($this->user2, Game::FLIP_TYPE_HEADS);
		$this->game->addPlayer($player2);
		$generatorStub = $this->getMockBuilder('AppBundle\Entity\RandomHeadTailsGenerator')->getMock();
		$generatorStub->method('doRandomFlip')->willReturn(Game::FLIP_TYPE_HEADS);
		$this->game->setRandomGenerator($generatorStub);
		$winner = $this->game->playGame();
		$this->assertEquals($winner, $player2);
	}
	
	public function testPlayGameReturnsPlayerWithTailsWhenTailsWins(){
		$player1 = new Player($this->user1, Game::FLIP_TYPE_TAILS);
		$this->game->addPlayer($player1);
		$player2 = new Player($this->user2, Game::FLIP_TYPE_HEADS);
		$this->game->addPlayer($player2);
		$generatorStub = $this->getMockBuilder('AppBundle\Entity\RandomHeadTailsGenerator')->getMock();
		$generatorStub->method('doRandomFlip')->willReturn(Game::FLIP_TYPE_TAILS);
		$this->game->setRandomGenerator($generatorStub);
		$winner = $this->game->playGame();
		$this->assertEquals($winner, $player1);
	}
	
	/**
	 * @expectedException AppBundle\Entity\NotEnoughPlayersException
	 */
	public function testCantPlayGameWithoutPlayersAdded(){
		$this->game->playGame();
	}
	
	/**
	 * @expectedException AppBundle\Entity\NotEnoughPlayersException
	 */
	public function testCantPlayGameWithOnlyOnePlayer(){
		$player1 = new Player($this->user1, Game::FLIP_TYPE_TAILS);
		$this->game->addPlayer($player1);
		$this->game->playGame();
	}
	
	public function testRemovePlayerRemovesPlayerFromGame(){
		$player1 = new Player($this->user1, Game::FLIP_TYPE_TAILS);
		$this->game->addPlayer($player1);
		$this->game->removePlayer($player1);
		$this->assertEquals(0, $this->game->getPlayerCount());
	}
	
	public function testGameStateInitializedForEmptyGame(){
		$this->assertEquals(Game::STATE_INITIALIZED, $this->game->getGameState());
	}
	
	public function testGameStateFinishedForPlayedGame(){
		$this->assertEquals(Game::STATE_INITIALIZED, $this->game->getGameState());
		$player1 = new Player($this->user1, Game::FLIP_TYPE_TAILS);
		$this->game->addPlayer($player1);
		$player2 = new Player($this->user2, Game::FLIP_TYPE_HEADS);
		$this->game->addPlayer($player2);
		$generatorStub = $this->getMockBuilder('AppBundle\Entity\RandomHeadTailsGenerator')->getMock();
		$generatorStub->method('doRandomFlip')->willReturn(Game::FLIP_TYPE_TAILS);
		$this->game->setRandomGenerator($generatorStub);
		$winner = $this->game->playGame();
		$this->assertEquals(Game::STATE_FINISHED, $this->game->getGameState());
	}

}
