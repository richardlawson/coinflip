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
	public function getReplacementGame(Game $game, GameNameFinder $gameNameFinder)
	{
		$replacement = new Game(new RandomHeadTailsGenerator());
		$replacement->setName($gameNameFinder->getUniqueName());
		$replacement->setReplacedGameId($game->getId());
		return $replacement;
	}
}
