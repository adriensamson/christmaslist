<?php

namespace Kyklydse\ChristmasListBundle\Controller;

use Kyklydse\ChristmasListBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    public function profileAction(Request $request)
    {
        $userForm = $this->createForm(new UserType(), $this->getUser());

        $userForm->handleRequest($request);
        if ($userForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_profile');
        }

        return $this->render('KyklydseChristmasListBundle:User:profile.html.twig', [
            'user' => $this->getUser(),
            'userForm' => $userForm->createView(),
            'friendsFriendsForm' => $this->getInviteFriendsFriendsForm()->createView(),
        ]);
    }

    private function getInviteFriendsFriendsForm()
    {
        $em = $this->getDoctrine()->getManager();
        $friendsFriends = $em->getRepository('KyklydseChristmasListBundle:User')->findFriendsFriends($this->getUser());
        $waitingInvitations  = $em->getRepository('KyklydseChristmasListBundle:User')->getWaitingInvitations($this->getUser());

        return $this->createFormBuilder(['invitedFriends' => $waitingInvitations], [
                'action' => $this->generateUrl('user_friends_friends'),
                'method' => 'POST',
            ])
            ->add('invitedFriends', 'entity', [
                'class' => 'KyklydseChristmasListBundle:User',
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false,
                'choices' => $friendsFriends,
                'label' => false,
            ])
            ->getForm();
    }

    public function inviteFriendsFriendsAction(Request $request)
    {
        $form = $this->getInviteFriendsFriendsForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            if ($form->getData() && $form->getData()['invitedFriends']) {

                $user = $this->getUser();
                $em = $this->getDoctrine()->getManager();
                $repo = $em->getRepository('KyklydseChristmasListBundle:User');
                foreach ($form->getData()['invitedFriends'] as $invitedFriend) {
                    $repo->inviteFriend($user, $invitedFriend);
                }

                $em->flush();
            }
        }

        return $this->redirectToRoute('user_profile');
    }
}
