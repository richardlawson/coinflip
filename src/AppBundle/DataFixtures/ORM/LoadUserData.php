<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		$userAdmin = new User();
	
        $userAdmin->setUsername('ricardo75');
        $userAdmin->setEmail('richard@network90.com');            
        $userAdmin->setPlainPassword('aberdeen');
        $userAdmin->setRoles(array('ROLE_SUPER_ADMIN'));
        $userAdmin->setEnabled(true);
        
        $user = new User();
        
        $user->setUsername('flipshark');
        $user->setEmail('lawson_richard@hotmail.com');
        $user->setPlainPassword('aberdeen');
        $user->setRoles(array('ROLE_USER'));
        $user->setEnabled(true);
        
        $user2 = new User();
        
        $user2->setUsername('elcondor');
        $user2->setEmail('richard@hotmail.com');
        $user2->setPlainPassword('aberdeen');
        $user2->setRoles(array('ROLE_USER'));
        $user2->setEnabled(true);

        $manager->persist($userAdmin);
        $manager->persist($user);
        $manager->persist($user2);
        $manager->flush();
		
		$this->addReference('user-ricardo75', $userAdmin);
		$this->addReference('user-flipshark', $user);
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getOrder()
	{
		return 1; // the order in which fixtures will be loaded
	}
}