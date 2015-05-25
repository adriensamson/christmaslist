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

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('KyklydseChristmasListBundle:User')->findOneBy(['facebookId' => $me->getId()]);

        if (!$user) {
            $user = new User();
            $user->setEmail($me->getEmail());
            $user->setUsername($me->getName());
            $user->setFacebookId($me->getId());
            $user->setPassword('');
            $user->setEnabled(true);

            $em->persist($user);
            $em->flush();
        }

        $this->updateFriends($session);

        $this->get('security.context')->setToken(new UsernamePasswordToken($user, null, 'main'));

        return $this->redirectToRoute('kyklydse_christmaslist_list_index');
    }

    private function updateFriends(FacebookSession $session)
    {
        $facebook = $this->get('kyklydse_christmas_list.facebook');
        $friendIds = $facebook->getFriendIds($session);
    }
}
