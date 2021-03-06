<?php

namespace Kyklydse\ChristmasListBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class ChristmasList
{
    /**
     * @var int
     */
    protected $id;
    
    /**
     * @var string
     */
    protected $name;
    
    /**
     * @var User[]
     */
    protected $owners;

    /**
     * @var Item[]
     */
    protected $items;

    /** @var  \DateTime */
    protected $date;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->owners = new ArrayCollection();
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

    public function canAccess(User $user)
    {
        if ($this->owners->contains($user)) {
            return true;
        }
        foreach ($this->owners as $owner) {
            if (in_array($user, $owner->getFriends(), true)) {
                return true;
            }
        }

        return false;
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

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }
}
