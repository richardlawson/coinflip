<?php

namespace AppBundle\Tests\Controller\Admin;

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
	
    public function testAdminCanAccessAdminArea()
    {
        $this->client = static::createClient();
		$crawler = $this->client->request('GET', '/admin');
		$crawler = $this->doLogin('ricardo75', 'aberdeen');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Admin Games Home")')->count() > 0);
    }
    
    public function testNormalUserCannotAccessAdminArea()
    {
    	$this->client = static::createClient();
    	$crawler = $this->client->request('GET', '/admin');
    	$crawler = $this->doLogin('flipshark', 'aberdeen');
    	$this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }
    
    public function testCanDeleteEmptyGame()
    {
    	$this->client = static::createClient();
		$crawler = $this->client->request('GET', '/admin');
		$crawler = $this->doLogin('ricardo75', 'aberdeen');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Arizona")')->count() > 0);
        $crawler = $this->client->request('GET', '/admin/game/delete/1');
        $crawler = $this->client->followRedirect();
        $this->assertTrue($crawler->filter('html:contains("Arizona")')->count() == 0);
    }
    
    public function testCannotDeleteGameWithPlayers()
    {
    	$this->client = static::createClient();
    	$crawler = $this->client->request('GET', '/admin');
    	$crawler = $this->doLogin('ricardo75', 'aberdeen');
    	$this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    	$this->assertTrue($crawler->filter('html:contains("Nevada")')->count() > 0);
    	$crawler = $this->client->request('GET', '/admin/game/delete/6');
    	$crawler = $this->client->followRedirect();
    	$this->assertTrue($crawler->filter('html:contains("Nevada")')->count() > 0);
    }
    
    public function testCanAddNewGame()
    {
    	$this->client = static::createClient();
    	$crawler = $this->client->request('GET', '/admin/game/add');
    	$crawler = $this->doLogin('ricardo75', 'aberdeen');
    	$this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    	$buttonCrawlerNode = $crawler->selectButton('game_save');
    	$form = $buttonCrawlerNode->form(array(
    		'game[name]' => 'South Carolina',
    	));
    	$this->client->submit($form);
    	$crawler = $this->client->followRedirect();
    	$this->assertTrue($crawler->filter('html:contains("South Carolina")')->count() > 0);
    }
    
    public function testCannotAddGameWithSameNameAsExistingLiveGame()
    {
    	$this->client = static::createClient();
    	$crawler = $this->client->request('GET', '/admin/game/add');
    	$crawler = $this->doLogin('ricardo75', 'aberdeen');
    	$this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    	$buttonCrawlerNode = $crawler->selectButton('game_save');
    	$form = $buttonCrawlerNode->form(array(
    			'game[name]' => 'Arizona',
    	));
    	$this->client->submit($form);
    	$this->assertRegexp(
	        '/already in use by another game/',
	        $this->client->getResponse()->getContent()
	    );
    }
    
    public function testCannotAddNameWithLessThanThreeChars()
    {
    	$this->client = static::createClient();
    	$crawler = $this->client->request('GET', '/admin/game/add');
    	$crawler = $this->doLogin('ricardo75', 'aberdeen');
    	$this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    	$buttonCrawlerNode = $crawler->selectButton('game_save');
    	$form = $buttonCrawlerNode->form(array(
    			'game[name]' => 'La',
    	));
    	$this->client->submit($form);
    	$this->assertRegexp(
	        '/Name must be at least 3 characters long/',
	        $this->client->getResponse()->getContent()
	    );
    }
    
    public function testCannotAddNameWithMoreThanTwentyFiveChars()
    {
    	$this->client = static::createClient();
    	$crawler = $this->client->request('GET', '/admin/game/add');
    	$crawler = $this->doLogin('ricardo75', 'aberdeen');
    	$this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    	$buttonCrawlerNode = $crawler->selectButton('game_save');
    	$form = $buttonCrawlerNode->form(array(
    			'game[name]' => 'A Game Name With More Than Twenty Five Chars',
    	));
    	$this->client->submit($form);
    	$this->assertRegexp(
    			'/Name cannot be longer than 25 characters/',
    			$this->client->getResponse()->getContent()
    	);
    }
    
    public function testCanEditGame()
    {
    	$this->client = static::createClient();
    	$crawler = $this->client->request('GET', '/admin');
    	$crawler = $this->doLogin('ricardo75', 'aberdeen');
    	$this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    	$this->assertTrue($crawler->filter('html:contains("Arizona")')->count() > 0);
    	$crawler = $this->client->request('GET', '/admin/game/edit/1');
    	$buttonCrawlerNode = $crawler->selectButton('game_save');
    	$form = $buttonCrawlerNode->form(array(
    			'game[name]' => 'Arizona New',
    	));
    	$this->client->submit($form);
    	$crawler = $this->client->followRedirect();
    	$this->assertTrue($crawler->filter('html:contains("Arizona New")')->count() > 0);
    }
    
    public function testCannotEditNameOfGameToANameThatIsInUseByOneOfTheOtherLiveGames()
    {
    	$this->client = static::createClient();
    	$crawler = $this->client->request('GET', '/admin');
    	$crawler = $this->doLogin('ricardo75', 'aberdeen');
    	$this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    	$this->assertTrue($crawler->filter('html:contains("Arizona")')->count() > 0);
    	$crawler = $this->client->request('GET', '/admin/game/edit/1');
    	$buttonCrawlerNode = $crawler->selectButton('game_save');
    	$form = $buttonCrawlerNode->form(array(
    			'game[name]' => 'Detroit',
    	));
    	$this->client->submit($form);
    	$this->assertRegexp(
	        '/already in use by another game/',
	        $this->client->getResponse()->getContent()
	    );
    }
}
