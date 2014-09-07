<?php

namespace Kyklydse\ChristmasListBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @var User[]
     * @MongoDB\ReferenceMany(targetDocument="User")
     */
    protected $owners;

    /**
     * @var User[]
     * @MongoDB\ReferenceMany(targetDocument="User")
     */
    protected $invitedUsers;

    /**
     * @MongoDB\EmbedMany(targetDocument="Item")
     */
    protected $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->owners = new ArrayCollection();
        $this->invitedUsers = new ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return \MongoId
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
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInvitedUsers()
    {
        return $this->invitedUsers;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function isInvited(User $user)
    {
        return $this->invitedUsers->contains($user);
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
