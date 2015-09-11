<?php
namespace AppBundle\Entity;

abstract class GameEncoder{
	
	abstract public function getLiteObject();
	
	/**
	 * Sets game
	 *
	 * @param Game $game
	 */
	public function setGame(Game $game){
		$this->game = $game;
	}
	
	/**
	 * Gets Game As array
	 *
	 * @return array
	 */
	public function getAsArray(){
		$gameArray = [];
		$gameArray['id'] = $this->game->getId();
		$gameArray['name'] = $this->game->getName();
		$gameArray['noPlayers'] = $this->game->getPlayerCount();
		$gameArray['state'] = $this->game->getGameState();
		$gameArray['replacedGameId'] = $this->game->getReplacedGameId();
		$gameArray['players'] = $this->getPlayersAsArray();
		return $gameArray;
	}
	
	/**
	 * Gets Players As array
	 *
	 * @return array
	 */
	protected function getPlayersAsArray(){
		$playersArray = [];
		foreach($this->game->getPlayers() as $player){
			$player = new PlayerJsonEncoder($player);
			$playersArray[] = $player->getAsArray();
		}
		return $playersArray;
	}
	
}