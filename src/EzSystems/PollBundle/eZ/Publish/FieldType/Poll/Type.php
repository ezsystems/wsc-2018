<?php

namespace EzSystems\PollBundle\eZ\Publish\FieldType\Poll;

use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;
use eZ\Publish\Core\FieldType\Value as BaseValue;
use eZ\Publish\SPI\FieldType\Nameable;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\ValidationError;
use eZ\Publish\SPI\FieldType\Value as SPIValue;
use eZ\Publish\Core\FieldType\Value as CoreValue;
use eZ\Publish\SPI\Persistence\Content\FieldValue as PersistenceValue;
use function is_array;

class Type extends FieldType implements Nameable
{
    public function getFieldTypeIdentifier()
    {
        return 'ezpoll';
    }

    protected $validatorConfigurationSchema = [
        'QuestionLengthValidator' => [
            'minStringLength' => [
                'type' => 'int',
                'default' => 0,
            ],
            'maxStringLength' => [
                'type' => 'int',
                'default' => null,
            ],
        ],
        'AnswerListValueValidator' => [
            'answerLimit' => [
                'type' => 'int',
                'default' => 0,
            ],
        ],
    ];

    /**
     * Inspects given $inputValue and potentially converts it into a dedicated value object.
     *
     * @param array|\EzSystems\PollBundle\eZ\Publish\FieldType\Poll\Value $inputValue
     *
     * @return \EzSystems\PollBundle\eZ\Publish\FieldType\Poll\Value The potentially converted and structurally plausible value.
     */
    protected function createValueFromInput($inputValue)
    {
        if (is_array($inputValue)) {
            $inputValue = new Value($inputValue['question'], $inputValue['answers'] );
        }

        return $inputValue;
    }

    /**
     * Validates field value against 'isMultiple' and 'options' settings.
     *
     * Does not use validators.
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     *
     * @param \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition $fieldDefinition The field definition of the field
     * @param \eZ\Publish\Core\FieldType\Selection\Value $fieldValue The field value for which an action is performed
     *
     * @return \eZ\Publish\SPI\FieldType\ValidationError[]
     */
    public function validate(FieldDefinition $fieldDefinition, SPIValue $fieldValue)
    {
        $validationErrors = [];

        if ($this->isEmptyValue($fieldValue)) {
            return $validationErrors;
        }

        $validatorConfiguration = $fieldDefinition->getValidatorConfiguration();
        $questionLengthConstraints = $validatorConfiguration['QuestionLengthValidator'] ?? [];
        $answerListValueValidatorConstraints = $validatorConfiguration['AnswerListValueValidator'] ?? [];

        if (isset($questionLengthConstraints['maxStringLength']) &&
            $questionLengthConstraints['maxStringLength'] !== false &&
            $questionLengthConstraints['maxStringLength'] !== 0 &&
            mb_strlen($fieldValue->question) > $questionLengthConstraints['maxStringLength']) {
            $validationErrors[] = new ValidationError(
                'The string can not exceed %size% character.',
                'The string can not exceed %size% characters.',
                array(
                    '%size%' => $questionLengthConstraints['maxStringLength'],
                ),
                'question'
            );
        }

        if (isset($questionLengthConstraints['minStringLength']) &&
            $questionLengthConstraints['minStringLength'] !== false &&
            $questionLengthConstraints['minStringLength'] !== 0 &&
            mb_strlen($fieldValue->question) < $questionLengthConstraints['minStringLength']) {
            $validationErrors[] = new ValidationError(
                'The string can not be shorter than %size% character.',
                'The string can not be shorter than %size% characters.',
                array(
                    '%size%' => $questionLengthConstraints['minStringLength'],
                ),
                'question'
            );
        }

        if (isset($constraints['answerLimit']) &&
            $answerListValueValidatorConstraints['answerLimit'] > 0 && count($fieldValue->answers) > $answerListValueValidatorConstraints['answerLimit']) {
            $validationErrors[] = new ValidationError(
                'The answers number cannot be higher than %limit%.',
                null,
                array(
                    '%limit%' => $answerListValueValidatorConstraints['answerLimit'],
                ),
                'answers'
            );
        }

        return $validationErrors;
    }

    public function validateValidatorConfiguration($validatorConfiguration)
    {
        $validationErrors = [];

        foreach ($validatorConfiguration as $validatorIdentifier => $constraints) {
            // Validate arguments from PollValueValidator
            foreach ($constraints as $name => $value) {
                switch ($name) {
                    case 'minStringLength':
                    case 'maxStringLength':
                        if ($value !== false && !is_int($value) && !(null === $value)) {
                            $validationErrors[] = new ValidationError(
                                "Validator parameter '%parameter%' value must be of integer type",
                                null,
                                array(
                                    '%parameter%' => $name,
                                )
                            );
                        } elseif ($value < 0) {
                            $validationErrors[] = new ValidationError(
                                "Validator parameter '%parameter%' value can't be negative",
                                null,
                                array(
                                    '%parameter%' => $name,
                                )
                            );
                        }
                        break;
                    case 'answerLimit':
                        if (!is_int($value) && !ctype_digit($value)) {
                            $validationErrors[] = new ValidationError(
                                "Validator parameter '%parameter%' value must be an integer",
                                null,
                                array(
                                    '%parameter%' => $name,
                                ),
                                "[$validatorIdentifier][$name]"
                            );
                        }
                        if ($value < 0) {
                            $validationErrors[] = new ValidationError(
                                "Validator parameter '%parameter%' value must be equal to/greater than 0",
                                null,
                                array(
                                    '%parameter%' => $name,
                                ),
                                "[$validatorIdentifier][$name]"
                            );
                        }
                        break;
                    default:
                        $validationErrors[] = new ValidationError(
                            "Validator parameter '%parameter%' is unknown",
                            null,
                            array(
                                '%parameter%' => $name,
                            )
                        );
                }
            }
        }

        return $validationErrors;
    }

    /**
     * Throws an exception if value structure is not of expected format.
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException If the value does not match the expected structure.
     *
     * @param \EzSystems\PollBundle\eZ\Publish\FieldType\Poll\Value $value
     */
    protected function checkValueStructure(BaseValue $value)
    {
        if (null !== $value->question && !is_string($value->question)) {
            throw new InvalidArgumentType(
                '$value->question',
                'string',
                $value->question
            );
        }

        if (!is_array($value->answers)) {
            throw new InvalidArgumentType(
                '$value->answers',
                'array',
                $value->answers
            );
        }
    }

    /**
     * Returns the fallback default value of field type when no such default
     * value is provided in the field definition in content types.
     *
     * @return \EzSystems\PollBundle\eZ\Publish\FieldType\Poll\Value
     */
    public function getEmptyValue()
    {
        return new Value();
    }

    public function getFieldName( SPIValue $value , FieldDefinition $fieldDefinition, $languageCode)
    {
        return (string)$value->question;
    }

    protected function getSortInfo(CoreValue $value)
    {
        return (string)$value->question;
    }

    public function getName(SPIValue $value)
    {
        throw new \RuntimeException(
            'Name generation provided via NameableField set via "ezpublish.fieldType.nameable" service tag'
        );
    }

    public function fromHash($hash)
    {
        if ($hash === null) {
            return $this->getEmptyValue();
        }

        return new Value($hash['question'], $hash['answers']);
    }

    public function toHash(SPIValue $value)
    {
        if ($this->isEmptyValue($value)) {
            return null;
        }

        return [
            'question' => $value->question,
            'answers' => $value->answers,
        ];
    }

    public function toPersistenceValue(SPIValue $value)
    {
        if ($value === null) {
            return new PersistenceValue(
                [
                    'data' => null,
                    'externalData' => null,
                    'sortKey' => null,
                ]
            );
        }

        return new PersistenceValue(
            [
                'data' => $this->toHash($value),
                'sortKey' => $this->getSortInfo($value),
            ]
        );
    }

    public function fromPersistenceValue(PersistenceValue $fieldValue)
    {
        if ($fieldValue->data === null) {
            return $this->getEmptyValue();
        }

        return new Value($fieldValue->data['question'], $fieldValue->data['answers']);
    }
}