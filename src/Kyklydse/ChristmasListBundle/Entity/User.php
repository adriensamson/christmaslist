<?php

namespace Kyklydse\ChristmasListBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Entity\User as BaseUser;

class User extends BaseUser
{
    protected $id;

    protected $facebookId;
    /**
     * @var Collection
     */
    private $friends;

    /**
     * @var Collection
     */
    private $invitedFriends;

    public function __construct()
    {
        parent::__construct();
        $this->friends = new ArrayCollection();
        $this->invitedFriends = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->getUsername();
    }

    /**
     * @return mixed
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * @param mixed $facebookId
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;
    }

    public function getFriends()
    {
        return $this->friends->toArray();
    }

    public function addFriend(User $user)
    {
        if (!$this->friends->contains($user)) {
            $this->friends->add($user);
        }
        if ($this->invitedFriends->contains($user)) {
            $this->removeInvitedFriend($user);
        }
    }

    public function getInvitedFriends()
    {
        return $this->invitedFriends->toArray();
    }

    public function addInvitedFriend(User $user)
    {
        $this->invitedFriends->add($user);
    }

    public function removeInvitedFriend(User $user)
    {
        $this->invitedFriends->removeElement($user);
    }
}
