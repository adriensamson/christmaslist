<?php

namespace Kyklydse\ChristmasListBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */
class ChristmasList
{
    /**
     * @MongoDB\Id
     */
    protected $id;
    
    /**
     * @MongoDB\String
     */
    protected $name;
    
    /**
     * @MongoDB\ReferenceOne(targetDocument="User")
     */
    protected $owner;
    
    /**
     * @MongoDB\EmbedMany(targetDocument="Item")
     */
    protected $items;
    public function __construct()
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set owner
     *
     * @param Kyklydse\ChristmasListBundle\Document\User $owner
     */
    public function setOwner(\Kyklydse\ChristmasListBundle\Document\User $owner)
    {
        $this->owner = $owner;
    }

    /**
     * Get owner
     *
     * @return Kyklydse\ChristmasListBundle\Document\User $owner
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Add item
     *
     * @param Kyklydse\ChristmasListBundle\Document\Item $item
     */
    public function addItem(\Kyklydse\ChristmasListBundle\Document\Item $item)
    {
        $this->items[] = $item;
    }

    /**
     * Get items
     *
     * @return Doctrine\Common\Collections\Collection $items
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }
}
