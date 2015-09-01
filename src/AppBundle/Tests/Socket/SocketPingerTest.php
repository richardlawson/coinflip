<?php
namespace AppBundle\Tests\Socket;

use AppBundle\Socket\SocketPinger;

class SocketPingerTest extends \PHPUnit_Framework_TestCase{
	
	public function setUp(){

	}
	
	public function testIsSocketWorkingForValidSocket(){
		$validHost = 'tcp://127.0.0.1';
		$validPort = 8080;
		$socketPinger = new SocketPinger($validHost, $validPort);
		$this->assertTrue($socketPinger->isSocketServerWorking());
	}
	
	public function testIsSocketWorkingForInvalidSocket(){
		$validHost = 'tcp://127.0.0.1';
		$invalidPort = 8089;
		$socketPinger = new SocketPinger($validHost, $invalidPort);
		$this->assertFalse($socketPinger->isSocketServerWorking());
	}
}
