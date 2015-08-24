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

        $manager->persist($userAdmin);
        $manager->flush();
		
		$this->addReference('user-admin', $userAdmin);
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getOrder()
	{
		return 1; // the order in which fixtures will be loaded
	}
}