<?php

namespace AppBundle\Security\Limitation;

use eZ\Publish\API\Repository\Values\User\Limitation;

class QuestionLimitation extends Limitation
{
    public const QUESTION = 'Question';

    /**
     * @see \eZ\Publish\API\Repository\Values\User\Limitation::getIdentifier()
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return self::QUESTION;
    }
}
