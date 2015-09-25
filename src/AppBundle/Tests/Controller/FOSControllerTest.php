<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\WebTestCase;

class FOSControllerTest extends WebTestCase
{	
	
    public function testUpdatePasswordRedirectsBackToSamePageWithSuccessMessage()
    {
        $this->client = static::createClient();
        $crawler = $this->doLogin('ricardo75', 'aberdeen');
        $crawler = $this->client->request('GET', '/profile/change-password');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $buttonCrawlerNode = $crawler->selectButton('Change password');
        $form = $buttonCrawlerNode->form(array(
        		'fos_user_change_password_form[current_password]' => 'aberdeen',
        		'fos_user_change_password_form[plainPassword][first]' => 'banana',
        		'fos_user_change_password_form[plainPassword][second]' => 'banana'
        ));
        $this->client->submit($form);
    	$crawler = $this->client->followRedirect();
    	$this->assertTrue($crawler->filter('html:contains("Your password has been updated")')->count() > 0);
    }
    
    public function testEditProfileRedirectsBackToSamePageWithSuccessMessage()
    {
    	$this->client = static::createClient();
    	$crawler = $this->doLogin('ricardo75', 'aberdeen');
    	$crawler = $this->client->request('GET', '/profile/edit');
    	$this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    	$buttonCrawlerNode = $crawler->selectButton('Update');
    	$form = $buttonCrawlerNode->form(array(
    		'fos_user_profile_form[current_password]' => 'aberdeen',
    	));
    	$this->client->submit($form);
    	$crawler = $this->client->followRedirect();
    	$this->assertTrue($crawler->filter('html:contains("Your details have been saved")')->count() > 0);
    }
}
