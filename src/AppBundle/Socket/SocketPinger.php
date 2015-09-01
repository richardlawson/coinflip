<?php
namespace AppBundle\Socket;

class SocketPinger{
	
	protected $host;
	protected $port;
	
	/**
	 * contructor
	 *
	 * @param string $host
	 * @param integer $port
	 */
	public function __construct($host, $port)
	{
		$this->host = $host;
		$this->port = $port;
	}
	
	/**
	 * Is game ready
	 *
	 * @return boolean
	 */
	public function isSocketServerWorking()
	{
		$isWorking = true;
    	$fp = @fsockopen($this->host, $this->port, $errno, $errstr, 30);
    	if(!$fp){
		   $isWorking = false;
		}else{
		    fclose($fp);
		    $isWorking = true;
		}
    	return $isWorking;
	}
}