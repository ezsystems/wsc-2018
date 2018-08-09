<?php

namespace EzSystems\PollBundle\Controller;

use EzSystems\PollBundle\Entity\PollVote;
use EzSystems\PollBundle\Form\Factory\FormFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use eZ\Publish\API\Repository\Repository;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Response;

class PollController extends Controller
{
    /** @var \EzSystems\PollBundle\Form\Factory\FormFactory */
    protected $formFactory;

    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    /**
     * @param \EzSystems\PollBundle\Form\Factory\FormFactory
     * @param \eZ\Publish\API\Repository\Repository $repository
     */
    public function __construct(FormFactory $formFactory,Repository $repository)
    {
        $this->formFactory = $formFactory;
        $this->repository = $repository;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request): Response
    {
        $page = $request->query->get('page') ?? 1;

        $pagerfanta = new Pagerfanta(
            new ArrayAdapter($this->getDoctrine()
                ->getRepository(PollVote::class)
                ->findAllOrderedByQuestion())
        );

        $pagerfanta->setMaxPerPage(2);
        $pagerfanta->setCurrentPage(min($page, $pagerfanta->getNbPages()));

        return $this->render('@EzSystemsPoll/admin/poll/list.html.twig', [
            'pager' => $pagerfanta,
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, int $fieldId): Response
    {
        $page = $request->query->get('page') ?? 1;

        $pollAnswers = $this->getDoctrine()
            ->getRepository(PollVote::class)
            ->findAnswersByFieldId($fieldId);

        $pagerfanta = new Pagerfanta(
            new ArrayAdapter($pollAnswers)
        );

        $pagerfanta->setMaxPerPage(2);
        $pagerfanta->setCurrentPage(min($page, $pagerfanta->getNbPages()));

        return $this->render('@EzSystemsPoll/admin/poll/show.html.twig', [
            'pager' => $pagerfanta,
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function voteAction(Request $request)
    {
        $contentId = $request->get('contentId');
        $contentService = $this->repository->getContentService();
        $content = $contentService->loadContent($contentId);
        $answers = $content->getFieldValue($request->get('fieldDefIdentifier'))->answers;
        // 1) build the form
        $pollData = new PollVote();
        $pollData->setFieldId($content->getField($request->get('fieldDefIdentifier'))->id);
        $pollForm = $this->formFactory->createPollForm($pollData, null, ['answers' => $answers]);

        // 2) handle the submit (will only happen on POST)
        $pollForm->handleRequest($request);

        if ($pollForm->isSubmitted() && $pollForm->isValid()) {

            // 3) save the User!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pollData);
            $entityManager->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->render('EzSystemsPollBundle:Poll:vote.html.twig');
        }

        return $this->render('EzSystemsPollBundle:Poll:vote.html.twig');
    }
}
