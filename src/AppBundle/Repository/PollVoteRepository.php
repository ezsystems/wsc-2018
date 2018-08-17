<?php

namespace AppBundle\Repository;

use AppBundle\Entity\PollVote;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;

final class PollVoteRepository
{
    /** @var \Doctrine\Common\Persistence\ManagerRegistry */
    private $managerRegistry;

    /**
     * @param \Doctrine\Common\Persistence\ManagerRegistry $managerRegistry
     */
    public function __construct(
        ManagerRegistry $managerRegistry
    ) {
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    private function getManager(): EntityManager
    {
        return $this->managerRegistry->getManagerForClass(PollVote::class);
    }

    public function findAllOrderedByQuestion()
    {
        return $this->getManager()
            ->createQuery(
                'SELECT p FROM AppBundle:PollVote p GROUP BY p.fieldId, p.id ORDER BY p.question ASC'
            )
            ->getResult();
    }

    public function findAnswersByFieldId(int $fieldId, int $contentId)
    {
        $query = $this->getManager()
            ->createQuery(
                'SELECT p FROM AppBundle:PollVote p WHERE p.fieldId = :fieldId ORDER BY p.id ASC'
            );
        $query->setParameter('fieldId', $fieldId);

        return $query->getResult();
    }
}
