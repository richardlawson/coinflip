<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as CoinFlipAssert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\GameRepository")
 * @ORM\Table(name="games")
 */
class Game{
	const FLIP_TYPE_HEADS = 1;
	const FLIP_TYPE_TAILS = 2;
	const FLIP_TYPE_HEADS_STRING = 'heads';
	const FLIP_TYPE_TAILS_STRING = 'tails';
	const PLAYERS_NEEDED = 2;
	const MAX_PLAYERS = 2;
	// aka waiting for players
	const STATE_INITIALIZED = 0;
	// we have a player but not enough yet
	const STATE_HAS_PLAYERS_BUT_NOT_READY = 1;
	// we have enough players to play game
	const STATE_READY_TO_PLAY = 2;
	const STATE_FINISHED = 3;

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
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 3,
     *      max = 25,
     *      minMessage = "Name must be at least {{ limit }} characters long",
     *      maxMessage = "Name cannot be longer than {{ limit }} characters"
     * )
     * @CoinFlipAssert\NameIsNotInUse(groups={"New"})
	 */
	protected $name;
	
	protected $oldName;
	
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
	
	public function __construct(RandomHeadTailsGenerator $generator)
	{
		$this->generator = $generator;
		$this->players = new ArrayCollection();
	}
	
	/**
	 * Add random generator
	 *
	 * @param RandomHeadTailsGenerator $generator
	 */
	public function setRandomGenerator(RandomHeadTailsGenerator $generator)
	{
		$this->generator = $generator;
	}
	
	/**
	 * Play game
	 *
	 * @return Player
	 */
	public function playGame()
	{
		if($this->gameState == SELF::STATE_FINISHED){
			throw new CannotPlayFinishedGameException();
		}
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
	public function isGameReady()
	{
		return ($this->gameState == self::STATE_READY_TO_PLAY);
	}
	
	/**
	 * Get heads or tails flip
	 *
	 * @return integer
	 */
	protected function doRandomFlip()
	{
		return $this->generator->doRandomFlip();
	}
	
	/**
	 * is flip type in use
	 *
	 * @param int $flipType
	 * @return boolean
	 */
	public function isFlipTypeInUse($flipType)
	{
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
	public function addPlayer(Player $player)
	{
		if($this->gameState == self::STATE_FINISHED){
			throw new CannotAlterFinishedGameException();
		}
		if($this->gameState == self::STATE_READY_TO_PLAY){
			throw new GameFullException();
		}
		if($this->playerAlreadyAdded($player)){
			throw new PlayerAlreadyAddedException();
		}
		if($this->isFlipTypeInUse($player->getFlipType())){
			throw new FlipTypeAlreadySelectedException();
		}
		$this->players[] = $player;
		if($this->getPlayerCount() == self::PLAYERS_NEEDED){
			$this->gameState = SELF::STATE_READY_TO_PLAY;
		}else{
			$this->gameState = SELF::STATE_HAS_PLAYERS_BUT_NOT_READY;
		}
	}
	
	/**
	 * Player already added
	 *
	 * @param Player $player
	 * @return boolean
	 */
	public function playerAlreadyAdded(Player $player){
		return in_array($player->getUser()->getId(), $this->getUserIds());
	}
	
	/**
	 * Is user in game
	 *
	 * @param User $user
	 * @return boolean
	 */
	public function isUserInGame(User $user){
		return in_array($user->getId(), $this->getUserIds());
	}
	
	/**
	 * Gets user ids of players
	 *
	 * @param User $user
	 * @return array
	 */
	protected function getUserIds(){
		$userIds = [];
		foreach($this->players as $existingPlayer){
			$userIds[] = $existingPlayer->getUser()->getId();
		}
		return $userIds;
	}

    /**
     * Remove player
     *
     * @param \AppBundle\Entity\Player $player
     */
    public function removePlayer(\AppBundle\Entity\Player $player)
    {
    	if($this->gameState == self::STATE_FINISHED){
    		throw new CannotAlterFinishedGameException();
    	}
        $this->players->removeElement($player);
        if($this->getPlayerCount() == 0){
        	$this->gameState = SELF::STATE_INITIALIZED;
        }else{
        	$this->gameState = SELF::STATE_HAS_PLAYERS_BUT_NOT_READY;
        }
    }
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
    	return $this->id;
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
    public function getPlayerCount()
    {
    	return count($this->players);
    }
    
    /**
     * Get player 
     * 
     * @param integer $id
     * @return Player
     */
    public function getPlayerByUserId($userId)
    {
    	foreach($this->players as $player){
			if($player->getUser()->getId() == $userId){
				return $player;
			}
		}
		// if we get to this point caller must have passed an id of a user who is not in game
    	throw new UserNotInGameException();
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
     * Set oldName
     *
     * @param string $oldName
     * @return Game
     */
    public function setOldName($oldName)
    {
    	$this->oldName = $oldName;
    
    	return $this;
    }
    
    /**
     * Get oldName
     *
     * @return string
     */
    public function getOldName()
    {
    	return $this->oldName;
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
    public static function getFlipTypes()
    {
    	return self::$flipTypes;
    }
    
    
    /**
     * Is valid flip type
     *
     * @param integer $flipType
     * @return boolean
     */
    public static function isValidFlipType($flipType)
    {
    	return in_array($flipType, self::getFlipTypes());
    }
    
    /**
     * Get fliptype as string
     * 
     * @param integer $flipType;
     * @return string
     */
    public static function getFlipTypeAsString($flipType)
    {
    	if(!self::isValidFlipType($flipType)){
    		throw new InvalidFlipTypeException();
    	}
    	if($flipType == self::FLIP_TYPE_HEADS){
    		return self::FLIP_TYPE_HEADS_STRING;
    	}else{
    		return self::FLIP_TYPE_TAILS_STRING;
    	}
    }
}
