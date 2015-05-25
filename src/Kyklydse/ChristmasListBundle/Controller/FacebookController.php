<?php

namespace Kyklydse\ChristmasListBundle\Controller;

use Facebook\FacebookSession;
use Facebook\GraphUser;
use Kyklydse\ChristmasListBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class FacebookController extends Controller
{
    public function redirectAction()
    {
        $facebook = $this->get('kyklydse_christmas_list.facebook');

        return new RedirectResponse($facebook->getLoginUrl());
    }

    public function callbackAction()
    {
        $facebook = $this->get('kyklydse_christmas_list.facebook');
        $session = $facebook->getSessionFromRedirect();
        $me = $facebook->getMe($session);
        /** @var GraphUser $me */

        $user = $this->getUser();
        if ($user) {
            if ($user->getFacebookId() && $user->getFacebookId() !== $me->getId()) {
                return $this->redirectToRoute('kyklydse_christmaslist_list_index');
            }
            $user->setFacebookId($me->getId());
        }

        $em = $this->getDoctrine()->getManager();
        if (!$user) {
            $user = $em->getRepository('KyklydseChristmasListBundle:User')->findOneBy(['facebookId' => $me->getId()]);
        }

        if (!$user) {
            $user = new User();
            $user->setEmail($me->getEmail());
            $user->setUsername($me->getName());
            $user->setFacebookId($me->getId());
            $user->setPassword('');
            $user->setEnabled(true);

            $em->persist($user);
        }

        $this->updateFriends($user, $session);
        $em->flush();
        $this->get('security.context')->setToken(new UsernamePasswordToken($user, null, 'main'));

        return $this->redirectToRoute('kyklydse_christmaslist_list_index');
    }

    private function updateFriends(User $user, FacebookSession $session)
    {
        $facebook = $this->get('kyklydse_christmas_list.facebook');
        $friendIds = $facebook->getFriendIds($session);

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('KyklydseChristmasListBundle:User');
        $friends = $repo->findByFacebookIds($friendIds);

        foreach ($friends as $friend) {
            $repo->makeFriends($user, $friend);
        }
    }
}
