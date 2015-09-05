<?php
namespace AppBundle\Entity;

abstract class PlayerEncoder{
	
	abstract public function getLiteObject();
	
	/**
	 * Sets player
	 *
	 * @param Player $player
	 */
	public function setPlayer(Player $player){
		$this->player = $player;
	}
	
	/**
	 * Gets Player As array
	 *
	 * @return array
	 */
	public function getAsArray(){
		$playerArray = [];
		$playerArray['id'] = $this->player->getId();
		$playerArray['username'] = $this->player->getUser()->getUsername();
		$playerArray['flipType'] = $this->player->getFlipType();
		$playerArray['flipTypeAsString'] = Game::getFlipTypeAsString($this->player->getFlipType());
		return $playerArray;
	}
	
}