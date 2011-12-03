<?php

namespace Kyklydse\ChristmasListBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ItemType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('title', 'text', array('label' => 'Title'))
            ->add('description', 'textarea', array('label' => 'Description'))
            ->add('url', 'url', array('required' => false, 'label' => 'Link for more info'))
        ;
    }
    
    public function getName()
    {
        return 'item';
    }
}