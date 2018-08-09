<?php
/**
 * Created by PhpStorm.
 * User: mikolaj
 * Date: 8/2/18
 * Time: 3:52 PM
 */

namespace EzSystems\PollBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnswerCollectionType extends AbstractType
{
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezplatform_fieldtype_ezpoll_answers';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'allow_add' => true,
            'allow_delete' => true,
            'entry_type' => TextType::class,
            'prototype' => true,
            'prototype_name' => '__index__',
        ]);

        $resolver->setRequired('answer_limit');
    }

    public function getParent()
    {
        return CollectionType::class;
    }
}