<?php

namespace Kyklydse\ChristmasListBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ListType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name', 'text', array('label' => 'List name'));
    }
    
    public function getName()
    {
        return 'list';
    }
}