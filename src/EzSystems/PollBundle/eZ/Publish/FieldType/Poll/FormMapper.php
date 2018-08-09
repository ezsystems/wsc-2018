<?php
namespace EzSystems\PollBundle\eZ\Publish\FieldType\Poll;

use EzSystems\PollBundle\Form\PollFieldType;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\PollBundle\Form\PollValueTransformer;
use Symfony\Component\Form\FormInterface;
use EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface;
use eZ\Publish\API\Repository\FieldTypeService;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class FormMapper implements FieldValueFormMapperInterface, FieldDefinitionFormMapperInterface
{
    /** @var FieldTypeService */
    private $fieldTypeService;

    public function __construct(FieldTypeService $fieldTypeService)
    {
        $this->fieldTypeService = $fieldTypeService;
    }
    /**
     * @param FormInterface $fieldDefinitionForm
     * @param FieldDefinitionData $data
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     */
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $fieldDefinitionForm
            ->add('minLength', IntegerType::class, [
                'required' => false,
                'property_path' => 'validatorConfiguration[QuestionLengthValidator][minStringLength]',
                'label' => 'field_definition.ezpoll.min_length',
                'attr' => ['min' => 0],
            ])
            ->add('maxLength', IntegerType::class, [
                'required' => false,
                'property_path' => 'validatorConfiguration[QuestionLengthValidator][maxStringLength]',
                'label' => 'field_definition.ezpoll.max_length',
                'attr' => ['min' => 0],
            ])
            ->add('answerLimit', IntegerType::class, [
                'required' => false,
                'empty_data' => 0,
                'property_path' => 'validatorConfiguration[AnswerListValueValidator][answerLimit]',
                'label' => /** @Desc("Selection limit") */ 'field_definition.ezpoll.answer_limit',
            ]);
    }

    /**
     * @param FormInterface $fieldForm
     * @param FieldData $data
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $fieldForm->getConfig();
        $names = $fieldDefinition->getNames();
        $label = $fieldDefinition->getName($formConfig->getOption('mainLanguageCode')) ?: reset($names);
        $fieldType = $this->fieldTypeService->getFieldType($fieldDefinition->fieldTypeIdentifier);
        $answerLimit = $fieldDefinition->validatorConfiguration['AnswerListValueValidator']['answerLimit'];
        $fieldForm
            ->add(
                $formConfig->getFormFactory()
                    ->createBuilder()
                    ->create(
                        'value',
                        PollFieldType::class,
                        [
                            'required' => false,
                            'label' => $label,
                            'answer_limit' => $answerLimit,
                        ]
                    )
                    // Deactivate auto-initialize as we're not on the root form.
                    ->setAutoInitialize(false)
                    ->addModelTransformer(new PollValueTransformer($fieldType, $answerLimit))
                    ->getForm()
            );
    }
}