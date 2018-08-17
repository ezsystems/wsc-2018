<?php

namespace AppBundle\eZ\Publish\FieldType\Poll;

use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\MVC\Symfony\FieldType\View\ParameterProviderInterface;
use AppBundle\Entity\PollVote;
use AppBundle\Form\Factory\FormFactory;

class ParameterProvider implements ParameterProviderInterface
{
    /** @var \AppBundle\Form\Factory\FormFactory */
    protected $formFactory;

    /**
     * @param \AppBundle\Form\Factory\FormFactory
     */
    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * Returns a hash of parameters to inject to the associated fieldtype's view template.
     * Returned parameters will only be available for associated field type.
     *
     * Key is the parameter name (the variable name exposed in the template, in the 'parameters' array).
     * Value is the parameter's value.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Field $field The field parameters are provided for.
     *
     * @return array
     */
    public function getViewParameters(Field $field)
    {
        $pollData = new PollVote();
        $pollData->setQuestion($field->value->question);
        $pollForm = $this->formFactory->createPollForm($pollData, null, $field->value->answers);

        return [
            'pollForm' => $pollForm->createView(),
        ];
    }
}
