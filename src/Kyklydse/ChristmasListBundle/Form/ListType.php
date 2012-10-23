<?php

namespace Kyklydse\ChristmasListBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('owner', null, array('label' => 'Owner'))
            ->add('name', null, array('label' => 'List name'));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Kyklydse\\ChristmasListBundle\\Document\\ChristmasList'));
    }
    
    public function getName()
    {
        return 'list';
    }
}