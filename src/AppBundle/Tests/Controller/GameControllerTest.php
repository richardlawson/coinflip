<?php

namespace AppBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use AppBundle\Entity\Game;

class GameControllerTest extends WebTestCase
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
		$crawler = $this->client->request('GET', '/secure/games');
		$crawler = $this->doLogin('ricardo75', 'aberdeen');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Detroit")')->count() > 0);
    }
    
    public function testView()
    {
    	$this->client = static::createClient();
    	$crawler = $this->client->request('GET', '/secure/game/1');
    	$crawler = $this->doLogin('ricardo75', 'aberdeen');
    	$this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    	$this->assertEquals('Arizona',$crawler->filter('h1')->first()->text());
    }
    
    public function testPlayerAddedToPlayerListWhenUserClicksHeads()
    {
    	$this->client = static::createClient();
    	$crawler = $this->client->request('GET', '/secure/game/1');
    	$crawler = $this->doLogin('ricardo75', 'aberdeen');
    	$this->assertFalse($crawler->filter('html:contains("ricardo75")')->count() > 0);
    	$form = $crawler->selectButton('headsFlip_heads')->form();
		$crawler = $this->client->submit($form);
		$this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    	$this->assertTrue($crawler->filter('html:contains("ricardo75")')->count() > 0);
	}
	
	public function testUserCanSeeBothFlipOptionsWhenGameHasNoPlayers()
	{
		$this->client = static::createClient();
		$crawler = $this->client->request('GET', '/secure/game/1');
		$crawler = $this->doLogin('ricardo75', 'aberdeen');
		$this->assertTrue($crawler->filter('#headsFlip_heads')->count() == 1);
		$this->assertTrue($crawler->filter('#tailsFlip_tails')->count() == 1);
	}
	
	public function testUserCantSeeFlipOptionsWhenAlreadyRegisteredForGame()
	{
		$this->client = static::createClient();
		$crawler = $this->client->request('GET', '/secure/game/5');
		$crawler = $this->doLogin('ricardo75', 'aberdeen');
		$this->assertTrue($crawler->filter('#headsFlip_heads')->count() == 0);
		$this->assertTrue($crawler->filter('#tailsFlip_tails')->count() == 0);
	}
	
	public function testSecondUserCanOnlySeeFlipOptionTailsWhenHeadsAlreadySelectedByFirst()
	{
		$this->client = static::createClient();
		$crawler = $this->client->request('GET', '/secure/game/5');
		$crawler = $this->doLogin('flipshark', 'aberdeen');
		$this->assertTrue($crawler->filter('#headsFlip_heads')->count() == 0);
		$this->assertTrue($crawler->filter('#tailsFlip_tails')->count() == 1);
	}
	
	public function testSecondUserCanOnlySeeFlipOptionHeadsWhenTailsAlreadySelectedByFirst()
	{
		$this->client = static::createClient();
		$crawler = $this->client->request('GET', '/secure/game/6');
		$crawler = $this->doLogin('flipshark', 'aberdeen');
		$this->assertTrue($crawler->filter('#headsFlip_heads')->count() == 1);
		$this->assertTrue($crawler->filter('#tailsFlip_tails')->count() == 0);
	}
	
	public function testUserGetsTakenToGamePlayPageWhenTheyJoinAGameWithAnExistingPlayer()
	{
		$this->client = static::createClient();
		$crawler = $this->client->request('GET', '/secure/game/5');
		$crawler = $this->doLogin('flipshark', 'aberdeen');
		$form = $crawler->selectButton('tailsFlip_tails')->form();
		$crawler = $this->client->submit($form);
		$this->assertTrue($this->client->getResponse()->isRedirect());
		$crawler = $this->client->followRedirect();
		$this->assertEquals('Game in progress:', $crawler->filter('h2')->first()->text());
	}
	
	public function testUserCantViewGamePlayForGameWhereTheyAreNotOneOfThePlayers()
	{
		$this->client = static::createClient();
		$crawler = $this->client->request('GET', '/secure/game-play/7');
		$crawler = $this->doLogin('elcondor', 'aberdeen');
		$this->assertTrue($this->client->getResponse()->isRedirect());
		$crawler = $this->client->followRedirect();
		$this->assertEquals('Games Home', $crawler->filter('h1')->first()->text());
	}
	
	public function testUserRemovedFromGameWhenTheyClickLeaveGameButton()
	{
		$this->client = static::createClient();
		$crawler = $this->client->request('GET', '/secure/game/5');
		$crawler = $this->doLogin('ricardo75', 'aberdeen');
		$form = $crawler->selectButton('removePlayer_leave game')->form();
		$crawler = $this->client->submit($form);
		$this->assertTrue($this->client->getResponse()->isRedirect());
		$crawler = $this->client->followRedirect();
		$this->assertTrue($crawler->filter('html:contains(".flash-notice")')->count() > 0);
	}
	
	public function testUserCantRemoveThemselvesFromAGameTheyAreNotPlayingIn()
	{
		$this->client = static::createClient();
		$crawler = $this->doLogin('elcondor', 'aberdeen');
		$crawler = $this->client->request('POST', '/secure/game-remove-player/7');
		$this->assertTrue($this->client->getResponse()->isRedirect());
		$crawler = $this->client->followRedirect();
		$this->assertEquals('Games Home', $crawler->filter('h1')->first()->text());
	}
	
	public function testUserCantSeeFlipOrLeaveFormsForFinishedGame()
	{
		$this->client = static::createClient();
		$crawler = $this->client->request('GET', '/secure/game/7');
		$crawler = $this->doLogin('flipshark', 'aberdeen');
		$this->assertTrue($crawler->filter('#tailsFlip_tails')->count() == 0);
		$this->assertTrue($crawler->filter('#headsFlip_heads')->count() == 0);
		$this->assertTrue($crawler->filter('#removePlayer_leave game')->count() == 0);
	}
}
