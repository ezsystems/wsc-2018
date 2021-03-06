<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace AppBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PollType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = array_filter($options['answers'], function($value) {
            return $value !== null;
        });
        $builder
            ->add(
                'question',
                TextType::class,
                [
                    'required' => $options['required'],
                ]
            )
            ->add(
                'answer',
                ChoiceType::class,
                [
                    'label' => /** @Desc("Answers") */ 'content.field_type.ezpoll.answers',
                    'choices' => $choices,
                    'choice_label' => function ($choiceValue, $key, $value) {
                        return $value;
                    },
                    'expanded' => true,
                    'multiple' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'ezpoll',
        ]);

        $resolver->setRequired('answers');
    }
}
