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
     * @MongoDB\ReferenceMany(targetDocument="User")
     */
    protected $owners;
    
    /**
     * @MongoDB\EmbedMany(targetDocument="Item")
     */
    protected $items;

    public function __construct()
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
        $this->owners = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param User $owner
     */
    public function addOwner(User $owner)
    {
        $this->owners[] = $owner;
    }

    /**
     * Get owner
     *
     * @return \Doctrine\Common\Collections\Collection $owner
     */
    public function getOwners()
    {
        return $this->owners;
    }

    public function isOwner(User $user)
    {
        return $this->owners->contains($user);
    }

    /**
     * Add item
     *
     * @param Item $item
     */
    public function addItem(Item $item)
    {
        $this->items[] = $item;
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection $items
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
