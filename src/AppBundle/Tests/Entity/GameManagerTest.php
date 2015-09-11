<?php
namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Game;
use AppBundle\Entity\GameManager;
use AppBundle\Entity\User;
use AppBundle\Entity\Player;
use AppBundle\Entity\RandomHeadTailsGenerator;


class GameManagerTest extends \PHPUnit_Framework_TestCase{
	
	protected $game;
	protected $user1;
	protected $user2;
	
	public function setUp(){
		// set up a finished game
		$this->game = new Game(new RandomHeadTailsGenerator());
		$this->game->setId(1);
		$this->user1 = new User();
		$this->user1->setId(1);
		$this->user1->setUsername('ricardo75');
		$player1 = new Player($this->user1, Game::FLIP_TYPE_HEADS);
		$this->game->addPlayer($player1);
		$this->user2 = new User();
		$this->user2->setId(2);
		$this->user2->setUsername('coolcat');
		$player2 = new Player($this->user2, Game::FLIP_TYPE_TAILS);
		$this->game->addPlayer($player2);
		$this->game->playGame();
	}
	
	
	public function testGetReplacementGameReturnsNewGame(){
		$gameManager = new GameManager();
		$replacement = $gameManager->getReplacementGame($this->game);
		$this->assertInstanceOf('\AppBundle\Entity\Game', $replacement);
		$this->assertNotEquals($replacement, $this->game);
	}	
	
	public function testGetReplacementGameHasInitializedState(){
		$gameManager = new GameManager();
		$replacement = $gameManager->getReplacementGame($this->game);
		$this->assertEquals(Game::STATE_INITIALIZED, $replacement->getGameState());
	}
	
	public function testGetReplacementGameHasZeroPlayers(){
		$gameManager = new GameManager();
		$replacement = $gameManager->getReplacementGame($this->game);
		$this->assertEquals(0, $replacement->getPlayerCount());
	}
	
	public function testGetReplacementGameReturnsGameWithReplacedGameIdSetToFinishedGame(){
		$gameManager = new GameManager();
		$replacement = $gameManager->getReplacementGame($this->game);
		$this->assertEquals(1, $replacement->getReplacedGameId());
	}
}
