<?php
/**
 * NovaeZMailingBundle Bundle.
 *
 * @package   Novactive\Bundle\eZMailingBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMailingBundle/blob/master/LICENSE MIT Licence
 */
declare(strict_types=1);

namespace Novactive\Bundle\eZMailingBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Novactive\Bundle\eZMailingBundle\Entity\MailingList as MailingListEntity;

/**
 * Class MailingList.
 */
class MailingList extends EntityRepository
{
    /**
     * @param array $filters
     *
     * @return MailingListEntity[]
     */
    public function findByFilters(array $filters = []): array
    {
        $qb = $this->createQueryBuilderForFilters($filters);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array $filters
     *
     * @return int
     */
    public function countByFilters(array $filters = []): int
    {
        $qb = $this->createQueryBuilderForFilters($filters);
        $qb->select($qb->expr()->count('ml.id'));

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param array $filters
     *
     * @return QueryBuilder
     */
    public function createQueryBuilderForFilters(array $filters = []): QueryBuilder
    {
        $qb = $this->createQueryBuilder('ml')->select('ml');
        if (isset($filters['query'])) {
            $query = $filters['query'];
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('ml.names', ':query')
                )
            )->setParameter('query', '%'.$query.'%');
        }

        return $qb;
    }
}
