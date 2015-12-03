<?php

namespace Kyklydse\ChristmasListBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;

class ItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array('label' => 'Title'))
            ->add('description', TextareaType::class, array('label' => 'Description'))
            ->add('url', UrlType::class, array('required' => false, 'label' => 'Link for more info'))
        ;
    }
    
    public function getBlockPrefix()
    {
        return 'item';
    }
}
