<?php

namespace Kyklydse\ChristmasListBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('content', TextareaType::class, array('label' => 'Comment'));
    }
    
    public function getBlockPrefix()
    {
        return 'comment';
    }
}
