<?php

namespace Kyklydse\ChristmasListBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Kyklydse\ChristmasListBundle\Form\ListType;
use Kyklydse\ChristmasListBundle\Form\ItemType;
use Kyklydse\ChristmasListBundle\Form\CommentType;
use Kyklydse\ChristmasListBundle\Document\ChristmasList;
use Kyklydse\ChristmasListBundle\Document\Item;
use Kyklydse\ChristmasListBundle\Document\Comment;

class ListController extends Controller
{
    /**
     * @Route("/lists")
     * @Template()
     */
    public function indexAction()
    {
        $lists = $this->get('doctrine.odm.mongodb.document_manager')
            ->getRepository('KyklydseChristmasListBundle:ChristmasList')
            ->findAll()
            ;
        $currentUser = $this->get('security.context')->getToken()->getUser();
        return array('lists' => $lists, 'current_user' => $currentUser);
    }
    
    /**
     * @Route("/list/id/{id}")
     * @Template()
     */
    public function viewAction($id)
    {
        $list = $this->get('doctrine.odm.mongodb.document_manager')
            ->getRepository('KyklydseChristmasListBundle:ChristmasList')
            ->find($id);
        if (!$list) {
            throw $this->createNotFoundException('No list found for id '.$id);
        }
        $currentUser = $this->get('security.context')->getToken()->getUser();
        return array('list' => $list, 'current_user' => $currentUser);
    }
    
    /**
     * @Route("/list/new")
     * @Template()
     */
    public function createAction(Request $request)
    {
        $list = new ChristmasList();
        $list->setOwner($this->get('security.context')->getToken()->getUser());
        $form = $this->createForm(new ListType(), $list);
        
        if ($request->getMethod() === 'POST') {
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                $dm = $this->get('doctrine.odm.mongodb.document_manager');
                $dm->persist($list);
                $dm->flush();
                
                return $this->redirect($this->generateUrl('kyklydse_christmaslist_list_index'));
            }
        }
        
        return array('form' => $form->createView());
    }
    
    /**
     * @Route("/list/item/new/{list_id}")
     * @Template()
     */
    public function newItemAction($list_id, Request $request)
    {
        $list = $this->get('doctrine.odm.mongodb.document_manager')
            ->getRepository('KyklydseChristmasListBundle:ChristmasList')
            ->find($list_id);
        if (!$list) {
            throw $this->createNotFoundException('No list found for id '.$list_id);
        }
        
        $item = new Item();
        $item->setProposer($this->get('security.context')->getToken()->getUser());
        $form = $this->createForm(new ItemType(), $item);
        
        if ($request->getMethod() === 'POST') {
            $form->bindRequest($request);
        
            if ($form->isValid()) {
                $list->addItem($item);
                $dm = $this->get('doctrine.odm.mongodb.document_manager');
                $dm->flush();
        
                return $this->redirect($this->generateUrl('kyklydse_christmaslist_list_view', array('id' => $list->getId())));
            }
        }
        
        return array('form' => $form->createView(), 'list' => $list);
    }
    
    /**
     * @Route("/list/item/comment/{list_id}/{item_id}")
     * @Template()
     */
    public function commentItemAction($list_id, $item_id, Request $request)
    {
        $list = $this->get('doctrine.odm.mongodb.document_manager')
            ->getRepository('KyklydseChristmasListBundle:ChristmasList')
            ->find($list_id);
        $items = $list->getItems()->filter(function ($e) use ($item_id) {return $e->getId() == $item_id;});
        $item = $items->first();
        
        if (!$item) {
            throw $this->createNotFoundException('No item found for id '.$item_id);
        }
        
        $comment = new Comment();
        $comment->setAuthor($this->get('security.context')->getToken()->getUser());
        $form = $this->createForm(new CommentType(), $comment);
        
        if ($request->getMethod() === 'POST') {
            $form->bindRequest($request);
        
            if ($form->isValid()) {
                $item->addComment($comment);
                $dm = $this->get('doctrine.odm.mongodb.document_manager');
                $dm->flush();
        
                return $this->redirect($this->generateUrl('kyklydse_christmaslist_list_view', array('id' => $list->getId())));
            }
        }
        
        return array('form' => $form->createView(), 'list' => $list, 'item' => $item);
    }
}
