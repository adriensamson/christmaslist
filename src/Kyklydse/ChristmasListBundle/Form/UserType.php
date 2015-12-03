<?php

namespace Kyklydse\ChristmasListBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UserType extends AbstractType
{
    public function getBlockPrefix()
    {
        return 'user';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username');
        $builder->add('email');
    }
}
