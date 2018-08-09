<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\PollBundle\Form;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @todo add validation
 */
class PollData
{
    /**
     * @var string
     */
    protected $question;

    /**
     * @var string|null
     */
    protected $answer;


    /**
     * @param string $question
     * @param string|null $answer
     */
    public function __construct(string $question, ?string $answer = null)
    {
        $this->question = $question;
        $this->answer = $answer;
    }

    /**
     * @return string
     */
    public function getQuestion(): string
    {
        return $this->question;
    }

    /**
     * @param string $question
     */
    public function setQuestion(string $question): void
    {
        $this->question = $question;
    }

    /**
     * @return string|null
     */
    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    /**
     * @param string $answer
     */
    public function setAnswer(string $answer): void
    {
        $this->answer = $answer;
    }

}
