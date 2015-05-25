<?php

namespace Kyklydse\ChristmasListBundle\Repository;

use Doctrine\ORM\EntityRepository;
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
}
