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
}
