<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\WebTestCase;

class DefaultControllerTest extends WebTestCase
{	
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
