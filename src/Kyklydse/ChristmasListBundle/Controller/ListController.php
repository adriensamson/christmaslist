<?php

namespace Kyklydse\ChristmasListBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Kyklydse\ChristmasListBundle\Form\ListType;
use Kyklydse\ChristmasListBundle\Form\ItemType;
use Kyklydse\ChristmasListBundle\Form\CommentType;
use Kyklydse\ChristmasListBundle\Entity\ChristmasList;
use Kyklydse\ChristmasListBundle\Entity\Item;
use Kyklydse\ChristmasListBundle\Entity\Comment;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ListController extends Controller
{
    /**
     * @Route("")
     * @Template()
     */
    public function indexAction()
    {
        $currentUser = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        /** @var EntityManager $em */
        $qb = $em->getRepository('KyklydseChristmasListBundle:ChristmasList')->createQueryBuilder('l');
        $qb->leftJoin('l.owners', 'o');
        $qb->leftJoin('l.invitedUsers', 'i');
        $qb->where('o = :user OR i = :user');
        $qb->setParameter(':user', $currentUser);
        $lists = $qb->getQuery()->getResult();
        return array('lists' => $lists, 'current_user' => $currentUser);
    }
    
    /**
     * @Route("/list/id/{id}")
     * @Security("list.isOwner(user) || list.isInvited(user)")
     * @Template()
     */
    public function viewAction(ChristmasList $list)
    {
        $currentUser = $this->get('security.context')->getToken()->getUser();
        return array('list' => $list, 'current_user' => $currentUser);
    }

    /**
     * @Route("/list/id/{id}/edit")
     * @Security("list.isOwner(user) || list.isInvited(user)")
     * @Template()
     */
    public function editAction(ChristmasList $list, Request $request)
    {
        $form = $this->createForm(new ListType(), $list);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirect($this->generateUrl('kyklydse_christmaslist_list_index'));
        }
        $currentUser = $this->get('security.context')->getToken()->getUser();

        return array('form' => $form->createView(), 'list' => $list, 'current_user' => $currentUser);
    }
    
    /**
     * @Route("/list/new")
     * @Template()
     */
    public function createAction(Request $request)
    {
        $list = new ChristmasList();
        $list->addOwner($this->get('security.context')->getToken()->getUser());
        $list->setName($this->get('translator')->trans('Christmas %year%', array('%year%' => date('Y'))));
        $form = $this->createForm(new ListType(), $list);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($list);
            $em->flush();

            return $this->redirect($this->generateUrl('kyklydse_christmaslist_list_index'));
        }

        return array('form' => $form->createView());
    }
    
    /**
     * @Route("/list/item/new/{id}")
     * @Security("list.isOwner(user) || list.isInvited(user)")
     * @Template()
     */
    public function newItemAction(ChristmasList $list, Request $request)
    {
        $item = new Item();
        $item->setProposer($this->get('security.context')->getToken()->getUser());
        $form = $this->createForm(new ItemType(), $item);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $list->addItem($item);
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirect($this->generateUrl('kyklydse_christmaslist_list_view', array('id' => $list->getId())));
        }
        
        return array('form' => $form->createView(), 'list' => $list);
    }
    
    /**
     * @Route("/list/item/edit/{id}/{item_id}")
     * @Security("list.isOwner(user) || list.isInvited(user)")
     * @Template()
     */
    public function editItemAction(ChristmasList $list, $item_id, Request $request)
    {
        $items = $list->getItems()->filter(function ($e) use ($item_id) {
            return $e->getId() == $item_id;
        });
        $item = $items->first();
    
        if (!$item) {
            throw $this->createNotFoundException('No item found for id '.$item_id);
        }
        if ($item->getProposer() != $this->get('security.context')->getToken()->getUser()) {
            throw new AccessDeniedException();
        }
    
        $form = $this->createForm(new ItemType(), $item);
    
        if ($request->getMethod() === 'POST') {
            $form->bind($request);
    
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->flush();
    
                return $this->redirect($this->generateUrl('kyklydse_christmaslist_list_view', array('id' => $list->getId())));
            }
        }
    
        return array('form' => $form->createView(), 'list' => $list, 'item' => $item);
    }
    
    /**
     * @Route("/list/item/comment/{id}/{item_id}")
     * @Security("list.isOwner(user) || list.isInvited(user)")
     * @Template()
     */
    public function commentItemAction(ChristmasList $list, $item_id, Request $request)
    {
        $items = $list->getItems()->filter(function ($e) use ($item_id) {return $e->getId() == $item_id;});
        $item = $items->first();
        
        if (!$item) {
            throw $this->createNotFoundException('No item found for id '.$item_id);
        }
        
        $comment = new Comment();
        $comment->setAuthor($this->get('security.context')->getToken()->getUser());
        $form = $this->createForm(new CommentType(), $comment);
        
        if ($request->getMethod() === 'POST') {
            $form->bind($request);
        
            if ($form->isValid()) {
                $item->addComment($comment);
                $em = $this->getDoctrine()->getManager();
                $em->flush();
        
                return $this->redirect($this->generateUrl('kyklydse_christmaslist_list_view', array('id' => $list->getId())));
            }
        }
        
        return array('form' => $form->createView(), 'list' => $list, 'item' => $item);
    }

    /**
     * @Route("/list/item/delete/{id}/{item_id}")
     * @Method("POST")
     * @Security("list.isOwner(user) || list.isInvited(user)")
     */
    public function deleteItemAction(ChristmasList $list, $item_id)
    {
        $items = $list->getItems()->filter(function ($e) use ($item_id) {
                return $e->getId() == $item_id;
            });
        $item = $items->first();

        if (!$item) {
            throw $this->createNotFoundException('No item found for id ' . $item_id);
        }

        if ($item->getProposer() !== $this->getUser()) {
            throw new AccessDeniedException();
        }

        $list->getItems()->removeElement($item);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($this->generateUrl('kyklydse_christmaslist_list_view', array('id' => $list->getId())));
    }
}
