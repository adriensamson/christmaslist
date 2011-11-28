<?php

namespace Kyklydse\ChristmasListBundle\DataFixtures\MongoDB;

use Kyklydse\ChristmasListBundle\Document\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    public function load($manager)
    {
        $fosUserManager = $this->container->get('fos_user.user_manager');
        
        $user1 = new User();
        $user1->setUsername('test1');
        $user1->setEmail('test1@example.com');
        $user1->setEnabled(true);
        $user1->setPlainPassword('test');
        $fosUserManager->updatePassword($user1);
        $manager->persist($user1);
        $this->addReference('test-user1', $user1);
        
        $user2 = new User();
        $user2->setUsername('test2');
        $user2->setEmail('test2@example.com');
        $user2->setEnabled(true);
        $user2->setPlainPassword('test');
        $fosUserManager->updatePassword($user2);
        $manager->persist($user2);
        $this->addReference('test-user2', $user2);
        
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 1;
    }
}

