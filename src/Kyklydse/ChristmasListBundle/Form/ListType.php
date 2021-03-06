<?php

namespace Kyklydse\ChristmasListBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $owners = $options['data']->getOwners()->toArray();
        $users = $owners;
        foreach ($owners as $owner) {
            $users = array_merge($users, $owner->getFriends());
        }

        $builder
            ->add('owners', null, array(
                'label' => 'Owners',
                'expanded' => true,
                'choices' => $users,
            ))
            ->add('name', null, array('label' => 'List name'))
            ->add('date', 'date', array('widget' => 'single_text'))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Kyklydse\\ChristmasListBundle\\Entity\\ChristmasList'));
    }
    
    public function getBlockPrefix()
    {
        return 'list';
    }
}
