<?php
namespace AppBundle\Entity;

class RandomHeadTailsGenerator{
	
	/**
	 * Get radom flip
	 *
	 * @return integer
	 */
	public function doRandomFlip(){
		return mt_rand(Game::FLIP_TYPE_HEADS, Game::FLIP_TYPE_TAILS);
	}
	
}