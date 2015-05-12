<?php

namespace Kyklydse\ChristmasListBundle\DataFixtures\ORM;

use Kyklydse\ChristmasListBundle\Entity\ChristmasList;
use Kyklydse\ChristmasListBundle\Entity\Item;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadListData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $list = new ChristmasList();
        $list->setName('Test');
        $list->addOwner($this->getReference('test-user1'));
        $list->getInvitedUsers()->add($this->getReference('test-user2'));
        
        $item1 = new Item();
        $item1->setTitle('Test');
        $item1->setDescription('Description de test');
        $item1->setProposer($this->getReference('test-user1'));
        $list->addItem($item1);
        
        $item2 = new Item();
        $item2->setTitle('Proposé par un autre');
        $item2->setDescription('Invisible');
        $item2->setProposer($this->getReference('test-user2'));
        $list->addItem($item2);
        
        $manager->persist($list);
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 2;
    }
}

