<?php

namespace Kyklydse\ChristmasListBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
            ->add('name', null, array('label' => 'List name'));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Kyklydse\\ChristmasListBundle\\Entity\\ChristmasList'));
    }
    
    public function getName()
    {
        return 'list';
    }
}
