<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityManager;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\Core\Base\Exceptions\UnauthorizedException;

final class PollVoteRepository
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \eZ\Publish\Core\Repository\ContentService */
    private $contentService;

    public function __construct(
        EntityManager $entityManager,
        PermissionResolver $permissionResolver,
        ContentService $contentService
    )
    {
        $this->entityManager = $entityManager;
        $this->permissionResolver = $permissionResolver;
        $this->contentService = $contentService;
    }

    /**
     * @return mixed
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\Core\Base\Exceptions\UnauthorizedException
     */
    public function findAllOrderedByQuestion()
    {
        if ($this->permissionResolver->hasAccess('poll', 'list') !== true) {
            throw new UnauthorizedException('poll', 'list');
        }

        return $this->entityManager
            ->createQuery(
                'SELECT DISTINCT p.fieldId, p.contentId, p.question FROM AppBundle:PollVote p ORDER BY p.question ASC'
            )
            ->getResult();
    }

    /**
     * @param int $fieldId
     * @param int $contentId
     *
     * @return mixed
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\Core\Base\Exceptions\UnauthorizedException
     */
    public function findAnswersByFieldId(int $fieldId, int $contentId)
    {
        $content = $this->contentService->loadContent($contentId);

        if (!$this->permissionResolver->canUser('poll', 'show', $content)) {
            throw new UnauthorizedException('poll', 'show');
        }

        $query = $this->entityManager
            ->createQuery(
                'SELECT p FROM AppBundle:PollVote p WHERE p.fieldId = :fieldId ORDER BY p.id ASC'
            );
        $query->setParameter('fieldId', $fieldId);

        return $query->getResult();
    }
}
