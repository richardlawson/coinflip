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
	 * @ORM\ManyToOne(targetEntity="Game", inversedBy="products")
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
	
	public function __construct(User $user, $flipType){
		if(!$this->isValidFlipType($flipType)){
			throw new InvalidFlipTypeException();
		}
		$this->user = $user;
		$this->flipType = $flipType;
	}
	
	/**
	 * Is valid flip type
	 *
	 * @param integer $flipType
	 * @return boolean
	 */
	protected function isValidFlipType($flipType){
		return in_array($flipType, Game::getFlipTypes());
	}
	
	/**
     * Get user
     *
     * @return User
     */
	public function getUser(){
		return $this->user;
	}
	
	/**
	 * Get fliptype
	 *
	 * @return integer
	 */
	public function getFlipType(){
		return $this->flipType;
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
}
