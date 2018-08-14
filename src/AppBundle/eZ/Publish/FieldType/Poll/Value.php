<?php

namespace AppBundle\eZ\Publish\FieldType\Poll;

use eZ\Publish\Core\FieldType\Value as BaseValue;

class Value extends BaseValue
{
    /**
     * @var string
     */
    public $question;

    /**
     * @var array
     */
    public $answers;

    /**
     * Value constructor.
     *
     * @param string $question
     * @param string[] $answers
     */
    public function __construct(string $question = '', array $answers = [])
    {
        $this->question = $question;
        $this->answers = $answers;
    }

    public function __toString()
    {
        return $this->question;
    }
}
