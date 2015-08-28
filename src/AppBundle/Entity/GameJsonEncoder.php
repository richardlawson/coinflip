<?php
namespace AppBundle\Entity;

class GameJsonEncoder{
	
	protected $game;
	
	/**
	 * Object contructor
	 * 
	 * @param Game $game
	 */
	public function __construct(Game $game){
		$this->game = $game;
	}
	
	/**
	 * Gets basic json representaion of game
	 *
	 * @return string
	 */
	public function getLiteObject(){
		return json_encode($this->getAsArray());
	}
	
	protected function getAsArray(){
		$gameArray = [];
		$gameArray['id'] = $this->game->getId();
		$gameArray['name'] = $this->game->getName();
		$gameArray['noPlayers'] = $this->game->getPlayerCount();
		$gameArray['state'] = $this->game->getGameState();
		return $gameArray;
	}
	
}