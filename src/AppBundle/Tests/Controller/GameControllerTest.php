<?php

namespace AppBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class GameControllerTest extends WebTestCase
{
    protected $client;
    
	public function setUp(){
		$classes = array(
			'AppBundle\DataFixtures\ORM\LoadUserData',
			'AppBundle\DataFixtures\ORM\LoadGameData',
		);
		$this->loadFixtures($classes);
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
	
    public function testIndex()
    {
        $this->client = static::createClient();
		$crawler = $this->client->request('GET', '/games');
		$crawler = $this->doLogin('ricardo75', 'aberdeen');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Detroit")')->count() > 0);
    }
}
