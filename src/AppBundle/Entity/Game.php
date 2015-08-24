<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="games")
 */
class Game{
	const FLIP_TYPE_HEADS = 1;
	const FLIP_TYPE_TAILS = 2;
	const PLAYERS_NEEDED = 2;
	const STATE_INITIALIZED = 1;
	const STATE_FINISHED = 2;

	protected static $flipTypes = [self::FLIP_TYPE_HEADS,self::FLIP_TYPE_TAILS];
	
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\OneToMany(targetEntity="Player", mappedBy="game")
	 */
	protected $players;
	
	/**
	 * @ORM\Column(type="string", length=25)
	 */
	protected $name;
	
	/**
	 * @ORM\Column(type="smallint")
	 */
	protected $gameState = self::STATE_INITIALIZED;
	
	protected $generator;
	
	/**
	 * @ORM\OneToOne(targetEntity="Player")
	 * @ORM\JoinColumn(name="winner_id", referencedColumnName="id")
	 **/
	protected $winner;
	
	public function __construct(RandomHeadTailsGenerator $generator){
		$this->generator = $generator;
		$this->players = new ArrayCollection();
	}
	
	/**
	 * Add random generator
	 *
	 * @param RandomHeadTailsGenerator $generator
	 */
	public function setRandomGenerator(RandomHeadTailsGenerator $generator){
		$this->generator = $generator;
	}
	
	/**
	 * Play game
	 *
	 * @return Player
	 */
	public function playGame(){
		if(!$this->isGameReady()){
			throw new NotEnoughPlayersException();
		}
		$winningFlip = $this->doRandomFlip();
		foreach($this->players as $player){
			if($winningFlip == $player->getFlipType()){
				$this->winner = $player;
				break;
			}
		}
		$this->gameState = self::STATE_FINISHED;
		return $this->winner;
	}
	
	/**
	 * Is game ready
	 *
	 * @return boolean
	 */
	public function isGameReady(){
		return ($this->getPlayerCount() == self::PLAYERS_NEEDED);
	}
	
	/**
	 * Get heads or tails flip
	 *
	 * @return integer
	 */
	protected function doRandomFlip(){
		return $this->generator->doRandomFlip();
	}
	
	/**
	 * is flip type in use
	 *
	 * @param int $flipType
	 * @return boolean
	 */
	public function isFlipTypeInUse($flipType){
		$inUse = false;
		foreach($this->players as $player){
			if($flipType == $player->getFlipType()){
				$inUse = true;
				break;
			}
		}
		return $inUse;
	}
	
	/**
	 * Add player
	 *
	 * @param Player $player
	 */
	public function addPlayer(Player $player){
		if($this->isFlipTypeInUse($player->getFlipType())){
			throw new FlipTypeAlreadySelectedException();
		}
		$this->players[] = $player;
	}

    /**
     * Remove player
     *
     * @param \AppBundle\Entity\Player $player
     */
    public function removePlayer(\AppBundle\Entity\Player $player)
    {
        $this->players->removeElement($player);
    }

    /**
     * Get players
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPlayers()
    {
        return $this->players;
    }
    
    /**
     * Get player count
     *
     * @return integer
     */
    public function getPlayerCount(){
    	return count($this->players);
    }


    /**
     * Set name
     *
     * @param string $name
     * @return Game
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get gameState
     *
     * @return integer 
     */
    public function getGameState()
    {
        return $this->gameState;
    }

    /**
     * Set gameState
     *
     * @param integer $gameState
     * @return Game
     */
    public function setGameState($gameState)
    {
        $this->gameState = $gameState;

        return $this;
    }

    /**
     * Get winner
     *
     * @return \AppBundle\Entity\Player 
     */
    public function getWinner()
    {
        return $this->winner;
    }
    
    /**
     * Get fliptypes
     *
     * @return array
     */
    public static function getFlipTypes(){
    	return self::$flipTypes;
    }
}
