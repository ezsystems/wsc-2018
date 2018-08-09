<?php
/**
 * Created by PhpStorm.
 * User: mikolaj
 * Date: 8/6/18
 * Time: 2:25 PM
 */

namespace EzSystems\PollBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PollVoteRepository extends EntityRepository
{
    public function findAllOrderedByQuestion()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT p FROM EzSystemsPollBundle:PollVote p GROUP BY p.fieldId ORDER BY p.question ASC'
            )
            ->getResult();
    }

    /**
     * @param int $fieldId
     *
     * @return mixed
     */
    public function findAnswersByFieldId(int $fieldId)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT p FROM EzSystemsPollBundle:PollVote p WHERE p.fieldId = :fieldId ORDER BY p.id ASC'
            );
        $query->setParameter('fieldId', $fieldId);

        return $query->getResult();
    }
}