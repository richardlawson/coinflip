<?php
namespace AppBundle\Entity;

class GameJsonEncoder extends GameEncoder{
	
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
	
}