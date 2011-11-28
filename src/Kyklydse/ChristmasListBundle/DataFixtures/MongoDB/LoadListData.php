<?php

namespace Kyklydse\ChristmasListBundle\DataFixtures\MongoDB;

use Kyklydse\ChristmasListBundle\Document\ChristmasList;
use Kyklydse\ChristmasListBundle\Document\Item;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadListData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load($manager)
    {
        $list = new ChristmasList();
        $list->setName('Test');
        $list->setOwner($this->getReference('test-user1'));
        
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

