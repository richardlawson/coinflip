<?php
namespace AppBundle\Tests;

use Liip\FunctionalTestBundle\Test\WebTestCase as BaseWebTestCase;

class WebTestCase extends BaseWebTestCase{
	
	protected $client;
	
	public function setUp(){
		$classes = array(
				'AppBundle\DataFixtures\ORM\LoadUserData',
				'AppBundle\DataFixtures\ORM\LoadGameData',
				'AppBundle\DataFixtures\ORM\LoadPlayerData',
		);
		$this->loadFixtures($classes);
		$this->client = static::createClient();
	}
	
	public function doLogin($username, $password) {
		$crawler = $this->client->request('GET', '/login');
		$form = $crawler->selectButton('_submit')->form(array(
				'_username'  => $username,
				'_password'  => $password,
		));
		$this->client->submit($form);
	
		$this->assertTrue($this->client->getResponse()->isRedirect());
		$crawler = $this->client->followRedirect();
		return $crawler;
	}
}