<?php

namespace AppBundle\Repository;

use Doctrine\Common\Persistence\ManagerRegistry;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\PermissionResolver;
use AppBundle\Entity\PollVote;
use Doctrine\ORM\EntityManager;
use eZ\Publish\Core\Base\Exceptions\UnauthorizedException;

final class PollVoteRepository
{
    /** @var \Doctrine\Common\Persistence\ManagerRegistry */
    private $managerRegistry;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \eZ\Publish\Core\Repository\ContentService */
    private $contentService;

    /**
     * @param \Doctrine\Common\Persistence\ManagerRegistry $managerRegistry
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     */
    public function __construct(
        ManagerRegistry $managerRegistry,
        PermissionResolver $permissionResolver,
        ContentService $contentService
    ) {
        $this->managerRegistry = $managerRegistry;
        $this->permissionResolver = $permissionResolver;
        $this->contentService = $contentService;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    private function getManager(): EntityManager
    {
        return $this->managerRegistry->getManagerForClass(PollVote::class);
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

        return $this->getManager()
            ->createQuery(
                'SELECT p FROM AppBundle:PollVote p GROUP BY p.fieldId, p.id ORDER BY p.question ASC'
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

        $query = $this->getManager()
            ->createQuery(
                'SELECT p FROM AppBundle:PollVote p WHERE p.fieldId = :fieldId ORDER BY p.id ASC'
            );
        $query->setParameter('fieldId', $fieldId);

        return $query->getResult();
    }
}
