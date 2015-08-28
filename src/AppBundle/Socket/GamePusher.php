<?php
namespace AppBundle\Socket;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;

class GamePusher implements WampServerInterface {
	
	/**
	 * A lookup of all the topics clients have subscribed to. In our case, everyone should be subscribed to games
	 */
	protected $subscribedTopics = array();
	
	public function onSubscribe(ConnectionInterface $conn, $topic) 
	{
        $this->subscribedTopics[$topic->getId()] = $topic;
    }
	
	public function onUnSubscribe(ConnectionInterface $conn, $topic) 
	{
	}
	
	public function onOpen(ConnectionInterface $conn) 
	{
	}
	
	public function onClose(ConnectionInterface $conn) 
	{
	}
	
	public function onCall(ConnectionInterface $conn, $id, $topic, array $params) {
		// In this application if clients send data it's because the user hacked around in console
		$conn->callError($id, $topic, 'You are not allowed to make calls')->close();
	}
	
	public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible) {
		// In this application if clients send data it's because the user hacked around in console
		$conn->close();
	}
	
	public function onError(ConnectionInterface $conn, \Exception $e) {
	}
	
	/**
	 * @param string JSON'ified string we'll receive from ZeroMQ
	 */
	public function onGameUpdate($game) {
		echo "Game Update Called\r\n";
        // If the lookup topic object isn't set there is no one to publish 
        // in our case everyone should be subscribed to the games channel, so we should be okay
        if (!array_key_exists('games', $this->subscribedTopics)) {
            return;
        }

        $topic = $this->subscribedTopics['games'];

        // re-send the data to all the clients subscribed to that category
        $topic->broadcast($game);
	}
}
