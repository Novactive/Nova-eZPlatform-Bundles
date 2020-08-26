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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository as BaseEntityRepository;
use Doctrine\ORM\QueryBuilder;

abstract class EntityRepository extends BaseEntityRepository
{
    abstract protected function getAlias(): string;

    public function createQueryBuilderForFilters(array $filters = []): QueryBuilder
    {
        return $this->createQueryBuilder($this->getAlias())->select($this->getAlias())->distinct();
    }

    /**
     * @return array|ArrayCollection
     */
    public function findByFilters(array $filters = [])
    {
        $qb = $this->createQueryBuilderForFilters($filters);

        return $qb->getQuery()->getResult();
    }

    public function countByFilters(array $filters = []): int
    {
        $qb = $this->createQueryBuilderForFilters($filters);
        $qb->select($qb->expr()->countDistinct($this->getAlias().'.id'));

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
