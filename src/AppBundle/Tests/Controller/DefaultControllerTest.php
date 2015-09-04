<?php

namespace AppBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
	protected $client;
	
	public function setUp(){
		$classes = array(
				'AppBundle\DataFixtures\ORM\LoadUserData',
				'AppBundle\DataFixtures\ORM\LoadGameData',
				'AppBundle\DataFixtures\ORM\LoadPlayerData',
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
        $crawler = $this->client->request('GET', '/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Coin Flip', $crawler->filter('h1')->text());
    }
    
    public function testThatLoggedInUserGetsRedirectedToGamesHomeWhenTheyVisitIndex()
    {
    	$this->client = static::createClient();
    	$crawler = $this->doLogin('ricardo75', 'aberdeen');
    	$crawler = $this->client->request('GET', '/');
    	$this->assertTrue($this->client->getResponse()->isRedirect());
		$crawler = $this->client->followRedirect();
		$this->assertEquals('Games Home', $crawler->filter('h1')->first()->text());
    }
}
