<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

class GameManager{

	/**
	 * Get replacement game
	 *
	 * @param Game $game
	 * @return Game
	 */
	public function getReplacementGame(Game $game){
		$replacement = new Game(new RandomHeadTailsGenerator());
		$replacement->setName($game->getName());
		$replacement->setReplacedGameId($game->getId());
		return $replacement;
	}
}
