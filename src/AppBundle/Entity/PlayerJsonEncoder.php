<?php
namespace AppBundle\Entity;

class PlayerJsonEncoder extends PlayerEncoder{
	
	protected $player;
	
	/**
	 * Object contructor
	 * 
	 * @param Player $player
	 */
	public function __construct(Player $player){
		$this->player = $player;
	}
	
	/**
	 * Gets basic json representaion of player
	 *
	 * @return string
	 */
	public function getLiteObject(){
		return json_encode($this->getAsArray());
	}
	
}