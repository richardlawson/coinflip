<?php
namespace AppBundle\Socket;
use AppBundle\Entity\Game;
use AppBundle\Entity\GameJsonEncoder;

class GameUpdateNotifier{
	
	protected $socketAddress;
	protected $game;
	protected $gameEncoder;
	
	/**
	 * contructor
	 *
	 * @param string $socketAddress
	 * @param \AppBundle\Entity\Game $game
	 * @param \AppBundle\Entity\GameEncoder $gameEncoder (optional)
	 */
	public function __construct($socketAddress, Game $game, GameEncoder $gameEncoder = null)
	{
		$this->socketAddress = $socketAddress;
		$this->game = $game;
		if($gameEncoder == null){
			$this->gameEncoder = new GameJsonEncoder($game);
		}
	}
	
	public function notifySocketServer()
	{
		$encodedGame = $this->gameEncoder->getLiteObject();
		$context = new \ZMQContext();
		$socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'game pusher');
		$socket->connect($this->socketAddress);
		$socket->send($encodedGame);
	}
}