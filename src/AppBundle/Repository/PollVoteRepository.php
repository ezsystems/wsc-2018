<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityManager;

final class PollVoteRepository
{
    /**
     * @var EntityManager
     */
    private $entityManager;


    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findAllOrderedByQuestion()
    {
        return $this->entityManager
            ->createQuery(
                'SELECT DISTINCT p.fieldId, p.contentId, p.question FROM AppBundle:PollVote p ORDER BY p.question ASC'
            )
            ->getResult();
    }

    public function findAnswersByFieldId(int $fieldId, int $contentId)
    {
        $query = $this->entityManager
            ->createQuery(
                'SELECT p FROM AppBundle:PollVote p WHERE p.fieldId = :fieldId ORDER BY p.id ASC'
            );
        $query->setParameter('fieldId', $fieldId);

        return $query->getResult();
    }
}
