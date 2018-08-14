<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form Type representing ezpoll field type.
 */
class PollFieldType extends AbstractType
{
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezplatform_fieldtype_ezpoll';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'question',
                TextType::class,
                [
                    'required' => $options['required'],
                ]
            )
            ->add(
                'answers',
                AnswerCollectionType::class,
                [
                    'label' => /** @Desc("Alternative text") */ 'content.field_type.ezpoll.alternative_text',
                    'answer_limit' => $options['answer_limit']
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'ezpoll',
        ]);

        $resolver->setRequired('answer_limit');
    }
}
