<?php

namespace Kyklydse\ChristmasListBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Kyklydse\ChristmasListBundle\Entity\User;

class UserRepository extends EntityRepository
{
    public function findByFacebookIds(array $facebookIds)
    {
        $qb = $this->createQueryBuilder('f');
        $qb->andWhere('f.facebookId IN (:fbIds)')->setParameter(':fbIds', $facebookIds);

        return $qb->getQuery()->getResult();
    }

    public function makeFriends(User $user1, User $user2)
    {
        $user1->addFriend($user2);
        $user2->addFriend($user1);
    }

    public function inviteFriend(User $user1, User $user2)
    {
        if (in_array($user1, $user2->getInvitedFriends(), true)) {
            $this->makeFriends($user1, $user2);
            return;
        }
        $user1->addInvitedFriend($user2);
    }

    public function findFriendsFriends(User $user)
    {
        if (!count($user->getFriends())) {
            return [];
        }

        $qb = $this->createQueryBuilder('f')
            ->select('ff.id')
            ->innerJoin('f.friends', 'ff')
            ->andWhere('f.id IN (:friends)')
            ->andWhere('ff.id <> :user')
            ->andWhere('ff.id NOT IN (:friends)')
            ->setParameter(':user', $user->getId())
            ->setParameter(':friends', $user->getFriends());

        if (count($user->getInvitedFriends())) {
            $qb->andWhere('ff.id NOT IN (:invitedFriends)')->setParameter(':invitedFriends', $user->getInvitedFriends());
        }

        $friendsFriends = $qb->getQuery()->getResult();
        if (empty($friendsFriends)) {
            return [];
        }

        $qb = $this->createQueryBuilder('ff');
        $qb->andWhere('ff.id IN (:ff)');
        $qb->setParameter(':ff', $friendsFriends);

        return $qb->getQuery()->getResult();
    }
}
