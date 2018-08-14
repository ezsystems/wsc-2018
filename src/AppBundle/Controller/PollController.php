<?php

namespace AppBundle\Controller;

use eZ\Publish\API\Repository\NotificationService;
use AppBundle\Entity\PollVote;
use AppBundle\Form\Factory\FormFactory;
use AppBundle\Repository\PollVoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use eZ\Publish\API\Repository\Repository;

class PollController extends Controller
{
    /** @var \AppBundle\Form\Factory\FormFactory */
    protected $formFactory;

    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    /** @var \eZ\Publish\API\Repository\NotificationService */
    private $notificationService;

    /** @var \AppBundle\Repository\PollVoteRepository */
    private $poolVoteRepository;

    /**
     * @param \AppBundle\Form\Factory\FormFactory
     * @param \eZ\Publish\API\Repository\Repository $repository
     * @param \AppBundle\Repository\PollVoteRepository $poolVoteRepository
     * @param \eZ\Publish\API\Repository\NotificationService $notificationService
     */
    public function __construct(
        FormFactory $formFactory,
        Repository $repository,
        NotificationService $notificationService,
        PollVoteRepository $poolVoteRepository
    ) {
        $this->formFactory = $formFactory;
        $this->repository = $repository;
        $this->notificationService = $notificationService;
        $this->poolVoteRepository = $poolVoteRepository;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function voteAction(Request $request)
    {
        $contentId = $request->get('contentId');
        $contentService = $this->repository->getContentService();
        $content = $contentService->loadContent($contentId);
        $answers = $content->getFieldValue($request->get('fieldDefIdentifier'))->answers;

        $pollData = new PollVote();
        $pollData->setFieldId($content->getField($request->get('fieldDefIdentifier'))->id);
        $pollData->setContentId($content->id);
        $pollForm = $this->formFactory->createPollForm($pollData, null, $answers);

        $pollForm->handleRequest($request);

        if ($pollForm->isSubmitted() && $pollForm->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pollData);
            $entityManager->flush();

            return $this->render('AppBundle:Poll:vote.html.twig');
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
