<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="players")
 */
class Player{
	
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\ManyToOne(targetEntity="Game", inversedBy="players")
	 * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
	 */
	protected $game;
	
	/**
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 **/
	protected $user;
	
	/**
	 * @ORM\Column(type="smallint")
	 */
	protected $flipType;
	
	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $viewedGame = false;
	
	public function __construct(User $user, $flipType)
	{
		if(!Game::isValidFlipType($flipType)){
			throw new InvalidFlipTypeException();
		}
		$this->user = $user;
		$this->flipType = $flipType;
	}
	
	/**
     * Get user
     *
     * @return User
     */
	public function getUser()
	{
		return $this->user;
	}
	
	/**
	 * Get fliptype
	 *
	 * @return integer
	 */
	public function getFlipType()
	{
		return $this->flipType;
	}
	
	/**
	 * Get fliptype as string
	 *
	 * @return string
	 */
	public function getFlipTypeAsString()
	{
		return Game::getFlipTypeAsString($this->flipType);
	}

    /**
     * Set game
     *
     * @param \AppBundle\Entity\Game $game
     * @return Player
     */
    public function setGame(\AppBundle\Entity\Game $game = null)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game
     *
     * @return \AppBundle\Entity\Game 
     */
    public function getGame()
    {
        return $this->game;
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
     * Set viewedGame
     *
     * @param boolean $viewedGame
     * @return Player
     */
    public function setViewedGame($viewedGame)
    {
        $this->viewedGame = $viewedGame;

        return $this;
    }

    /**
     * Get viewedGame
     *
     * @return boolean 
     */
    public function getViewedGame()
    {
        return $this->viewedGame;
    }

}
