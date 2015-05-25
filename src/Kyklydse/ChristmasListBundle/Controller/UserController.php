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
        ]);
    }

    public function inviteFriendsFriendsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $friendsFriends = $em->getRepository('KyklydseChristmasListBundle:User')->findFriendsFriends($this->getUser());

        $form = $this->createFormBuilder($this->getUser())
            ->add('invitedFriends', null, ['expanded' => true, 'by_reference' => false, 'choices' => $friendsFriends, 'label' => ' '])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_profile');
        }

        return $this->render('KyklydseChristmasListBundle:User:inviteFriendsFriends.html.twig', ['form' => $form->createView()]);
    }
}
