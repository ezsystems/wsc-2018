<?php
/**
 * Created by PhpStorm.
 * User: mikolaj
 * Date: 8/2/18
 * Time: 10:10 AM
 */

namespace EzSystems\PollBundle\Form;

use Symfony\Component\Form\DataTransformerInterface;
use eZ\Publish\API\Repository\FieldType;
use eZ\Publish\Core\FieldType\Value;

class PollValueTransformer implements DataTransformerInterface
{
    /**
     * @var FieldType
     */
    private $fieldType;

    /** @var int */
    private $answerLimit;

    public function __construct(FieldType $fieldType, $answerLimit)
    {
        $this->fieldType = $fieldType;
        $this->answerLimit = $answerLimit;
    }

    /**
     * @param mixed $value
     *
     * @return array|null
     */
    public function transform($value): ?array
    {
        if (!$value instanceof Value) {
            return null;
        }

        $answers = array_replace(array_fill(0, $this->answerLimit, ''), $value->answers);

        return  ['question' => $value->question, 'answers' => $answers];
    }

    public function reverseTransform($value)
    {
        return $this->fieldType->fromHash($value);
    }
}