<?php

namespace AppBundle\Form\Factory;

use AppBundle\Entity\PollVote;
use AppBundle\Form\PollType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Util\StringUtil;

class FormFactory
{
    /** @var \Symfony\Component\Form\FormFactoryInterface */
    protected $formFactory;

    /**
     * @param \Symfony\Component\Form\FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param PollVote|null $data
     * @param string|null $name
     * @param array $answers
     *
     * @return FormInterface
     *
     */
    public function createPollForm(
        PollVote $data,
        ?string $name = null,
        array $answers
    ): FormInterface {
        $name = $name ?: StringUtil::fqcnToBlockPrefix(PollType::class);
        $options = null !== $answers ? ['answers' => $answers] : [];

        return $this->formFactory->createNamed(
            $name,
            PollType::class,
            $data,
            $options
        );
    }
}