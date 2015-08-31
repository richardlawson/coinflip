<?php
namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Game;
use AppBundle\Entity\User;
use AppBundle\Entity\Player;
use AppBundle\Entity\RandomHeadTailsGenerator;


class GameTest extends \PHPUnit_Framework_TestCase{
	
	protected $game;
	protected $user1;
	protected $user2;
	
	public function setUp(){
		$this->game = new Game(new RandomHeadTailsGenerator());
		$this->user1 = new User();
		$this->user1->setId(1);
		$this->user1->setUsername('ricardo75');
		$this->user2 = new User();
		$this->user2->setId(2);
		$this->user2->setUsername('coolcat');
		$this->user3 = new User();
		$this->user3->setId(3);
		$this->user2->setUsername('flipshark');
	}
	
	public function testGameObjectCreated(){
		$this->assertInstanceOf('AppBundle\Entity\Game', $this->game);
	}
	
/* -------------- TEST ADD PLAYER METHOD -------------------------------------------*/
	
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
	 * @expectedException AppBundle\Entity\PlayerAlreadyAddedException
	 */
	public function testCantAddPlayerToGameMoreThanOnce(){
		$player1 = new Player($this->user1, Game::FLIP_TYPE_TAILS);
		$this->game->addPlayer($player1);
		$this->game->addPlayer($player1);
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
	
	/**
	 * @expectedException AppBundle\Entity\GameFullException
	 */
	public function testCantAddMoreThanTwoPlayers(){
		$player1 = new Player($this->user1, Game::FLIP_TYPE_TAILS);
		$this->game->addPlayer($player1);
		$player2 = new Player($this->user2, Game::FLIP_TYPE_HEADS);
		$this->game->addPlayer($player2);
		$player3 = new Player($this->user3, Game::FLIP_TYPE_HEADS);
		$this->game->addPlayer($player3);
	}
	
	/**
	 * @expectedException AppBundle\Entity\CannotAlterFinishedGameException
	 */
	public function testCantAddPlayerToFinishedGame(){
		$player1 = new Player($this->user1, Game::FLIP_TYPE_TAILS);
		$this->game->addPlayer($player1);
		$player2 = new Player($this->user2, Game::FLIP_TYPE_HEADS);
		$this->game->addPlayer($player2);
		$this->game->playGame();
		$player3 = new Player($this->user3, Game::FLIP_TYPE_HEADS);
		$this->game->addPlayer($player3);
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
	
/* -------------- TEST PLAY GAME METHOD -------------------------------------------*/
	
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
	 * @expectedException AppBundle\Entity\CannotPlayFinishedGameException
	 */
	public function testCantReplayFinishedGame(){
		$player1 = new Player($this->user1, Game::FLIP_TYPE_TAILS);
		$this->game->addPlayer($player1);
		$player2 = new Player($this->user2, Game::FLIP_TYPE_HEADS);
		$this->game->addPlayer($player2);
		$this->game->playGame();
		$this->game->playGame();
	}
	
/* -------------- TEST REMOVE PLAYER METHOD -------------------------------------------*/
	
	
	public function testRemovePlayerFromNoPlayerGameDoesNothing(){
		$player1 = new Player($this->user1, Game::FLIP_TYPE_TAILS);
		$this->game->removePlayer($player1);
		$this->assertEquals(0, $this->game->getPlayerCount());
	}
	
	public function testRemovePlayerRemovesPlayerFromGame(){
		$player1 = new Player($this->user1, Game::FLIP_TYPE_TAILS);
		$this->game->addPlayer($player1);
		$this->game->removePlayer($player1);
		$this->assertEquals(0, $this->game->getPlayerCount());
	}
	
	public function testRemovePlayerFromTwoPlayerRemovesPlayerFromGame(){
		$player1 = new Player($this->user1, Game::FLIP_TYPE_TAILS);
		$this->game->addPlayer($player1);
		$player2 = new Player($this->user2, Game::FLIP_TYPE_HEADS);
		$this->game->addPlayer($player2);
		$this->assertEquals(2, $this->game->getPlayerCount());
		$this->game->removePlayer($player2);
		$this->assertEquals(1, $this->game->getPlayerCount());
	}
	
	/**
	 * @expectedException AppBundle\Entity\CannotAlterFinishedGameException
	 */
	public function testCantRemovePlayerFromFinishedGames(){
		$player1 = new Player($this->user1, Game::FLIP_TYPE_TAILS);
		$this->game->addPlayer($player1);
		$player2 = new Player($this->user2, Game::FLIP_TYPE_HEADS);
		$this->game->addPlayer($player2);
		$generatorStub = $this->getMockBuilder('AppBundle\Entity\RandomHeadTailsGenerator')->getMock();
		$generatorStub->method('doRandomFlip')->willReturn(Game::FLIP_TYPE_TAILS);
		$this->game->setRandomGenerator($generatorStub);
		$winner = $this->game->playGame();
		$this->game->removePlayer($player1);
	}
	
/* -------------- TEST VARIOUS UTIL METHODS -------------------------------------------*/
	
	public function testIsUserInGame(){
		$player1 = new Player($this->user1, Game::FLIP_TYPE_TAILS);
		$this->game->addPlayer($player1);
		$this->assertTrue($this->game->isUserInGame($this->user1));
	}
	
	/**
	 * @expectedException AppBundle\Entity\InvalidFlipTypeException
	 */
	public function testExceptionThrownWhenFlipTypeNotVaild(){
		$invalidFlipType = 3;
		Game::getFlipTypeAsString($invalidFlipType);
	}
	
	public function testGetFlipTypeAsStringReturnsHeadsStringForHeadsFlipType(){
		$expected = 'heads';
		$this->assertEquals($expected, Game::getFlipTypeAsString(Game::FLIP_TYPE_HEADS));
	}
	
	public function testGetFlipTypeAsStringReturnsTailsStringForTailsFlipType(){
		$expected = 'tails';
		$this->assertEquals($expected, Game::getFlipTypeAsString(Game::FLIP_TYPE_TAILS));
	}
	
	public function testIsValidFlipTypeReturnsTrueForValidFlipTypes(){
		$this->assertTrue(Game::isValidFlipType(Game::FLIP_TYPE_HEADS));
		$this->assertTrue(Game::isValidFlipType(Game::FLIP_TYPE_TAILS));
	}
	
	public function testIsValidFlipTypeReturnsFalseForInvalidFlipTypes(){
		$invalidType1 = 0;
		$invalidType2 = 3;
		$this->assertFalse(Game::isValidFlipType($invalidType1));
		$this->assertFalse(Game::isValidFlipType($invalidType2));
	}
	
	public function testGetPlayerByUserId(){
		$player1 = new Player($this->user1, Game::FLIP_TYPE_HEADS);
		$this->game->addPlayer($player1);
		$this->assertEquals($player1, $this->game->getPlayerByUserId($this->user1->getId()));
		$player2 = new Player($this->user2, Game::FLIP_TYPE_TAILS);
		$this->game->addPlayer($player2);
		$this->assertEquals($player2, $this->game->getPlayerByUserId($this->user2->getId()));
	}
	
	/**
	 * @expectedException AppBundle\Entity\UserNotInGameException
	 */
	public function testGetPlayerByUserIdThrowsExceptionForUserNotInGame(){
		$idOfUserNotInGame = 5;
		$player = $this->game->getPlayerByUserId($idOfUserNotInGame);
	}
	
/* -------------- TEST GAME STATES -------------------------------------------*/
	
	public function testGameStateInitializedForEmptyGame(){
		$this->assertEquals(Game::STATE_INITIALIZED, $this->game->getGameState());
	}
	
	public function testGameStateHasPlayersWhenOnePlayerAdded(){
		$this->assertEquals(Game::STATE_INITIALIZED, $this->game->getGameState());
		$player1 = new Player($this->user1, Game::FLIP_TYPE_TAILS);
		$this->game->addPlayer($player1);
		$this->assertEquals(Game::STATE_HAS_PLAYERS_BUT_NOT_READY, $this->game->getGameState());
	}
	
	public function testGameStateReadyWhenTwoValidPlayersAdded(){
		$this->assertEquals(Game::STATE_INITIALIZED, $this->game->getGameState());
		$player1 = new Player($this->user1, Game::FLIP_TYPE_TAILS);
		$this->game->addPlayer($player1);
		$player2 = new Player($this->user2, Game::FLIP_TYPE_HEADS);
		$this->game->addPlayer($player2);
		$this->assertEquals(Game::STATE_READY_TO_PLAY, $this->game->getGameState());
	}
	
	public function testGameStateFinishedForPlayedGame(){
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
	
	public function testGameStateGoesBackToInitializedWhenPlayerRemovedFromOnePlayerGame(){
		$this->assertEquals(Game::STATE_INITIALIZED, $this->game->getGameState());
		$player1 = new Player($this->user1, Game::FLIP_TYPE_TAILS);
		$this->game->addPlayer($player1);
		$this->assertEquals(Game::STATE_HAS_PLAYERS_BUT_NOT_READY, $this->game->getGameState());
		$this->game->removePlayer($player1);
		$this->assertEquals(Game::STATE_INITIALIZED, $this->game->getGameState());
	}
	
	public function testGameStateGoesBackToReadyWhenPlayerRemovedFromTwoPlayerGame(){
		$this->assertEquals(Game::STATE_INITIALIZED, $this->game->getGameState());
		$player1 = new Player($this->user1, Game::FLIP_TYPE_TAILS);
		$this->game->addPlayer($player1);
		$player2 = new Player($this->user2, Game::FLIP_TYPE_HEADS);
		$this->game->addPlayer($player2);
		$this->assertEquals(Game::STATE_READY_TO_PLAY, $this->game->getGameState());
		$this->game->removePlayer($player1);
		$this->assertEquals(Game::STATE_HAS_PLAYERS_BUT_NOT_READY, $this->game->getGameState());
	}

}
