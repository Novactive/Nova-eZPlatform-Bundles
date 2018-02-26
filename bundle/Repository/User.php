<?php
/**
 * NovaeZMailingBundle Bundle.
 *
 * @package   Novactive\Bundle\eZMailingBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/eZMailingBundle/blob/master/LICENSE MIT Licence
 */
declare(strict_types=1);

namespace Novactive\Bundle\eZMailingBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Novactive\Bundle\eZMailingBundle\Entity\User as UserEntity;

/**
 * Class User.
 */
class User extends EntityRepository
{
    /**
     * @param array $filters
     *
     * @return UserEntity[]
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
        $qb->select($qb->expr()->count('u.id'));

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param array $filters
     *
     * @return QueryBuilder
     */
    public function createQueryBuilderForFilters(array $filters = []): QueryBuilder
    {
        $qb = $this->createQueryBuilder('u')->select('u');
        $qb->where($qb->expr()->eq('u.restricted', ':restricted'))->setParameter('restricted', false);

        if (isset($filters['mailingLists'])) {
            $qb
                ->innerJoin(
                    'u.registrations',
                    'reg',
                    Join::WITH,
                    $qb->expr()->in('reg.mailingList', ':mailinglists')
                )
                ->setParameter('mailinglists', $filters['mailingLists']);
        }

        if (isset($filters['status'])) {
            $qb->andWhere($qb->expr()->in('u.status', $filters['status']));
        }

        if (isset($filters['query'])) {
            $query = $filters['query'];
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('u.email', ':query'),
                    $qb->expr()->like('u.firstName', ':query'),
                    $qb->expr()->like('u.lastName', ':query')
                )
            )->setParameter('query', '%'.$query.'%');
        }

        return $qb;
    }
}
