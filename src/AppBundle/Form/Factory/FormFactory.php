<?php

namespace AppBundle\Form\Factory;

use AppBundle\Entity\PollVote;
use AppBundle\Form\PollType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Util\StringUtil;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FormFactory
{
    /** @var \Symfony\Component\Form\FormFactoryInterface */
    protected $formFactory;

    /** @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface */
    protected $urlGenerator;

    /**
     * @param \Symfony\Component\Form\FormFactoryInterface $formFactory
     * @param \Symfony\Component\Routing\Generator\UrlGeneratorInterface $urlGenerator
     */
    public function __construct(FormFactoryInterface $formFactory, UrlGeneratorInterface $urlGenerator)
    {
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
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
        $options = null !== $answers
            ? ['answers' => $answers]
            : [];

        return $this->formFactory->createNamed(
            $name,
            PollType::class,
            $data,
            $options
        );
    }
}
