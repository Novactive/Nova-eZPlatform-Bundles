<?php

/**
 * NovaeZProtectedContentBundle.
 *
 * @package   Novactive\Bundle\eZProtectedContentBundle
 *
 * @author    Novactive
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/eZProtectedContentBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZProtectedContentBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

abstract class EntityRepository extends ServiceEntityRepository
{
    abstract protected function getAlias(): string;

    // Problème :
    // Novactive\Bundle\eZProtectedContentBundle\Repository\EntityRepository::__construct():
    // Argument #1 ($registry) must be of type Doctrine\Persistence\ManagerRegistry,
    // Doctrine\ORM\EntityManager given,
    // called in /var/www/html/ibexa/vendor/doctrine/orm/lib/Doctrine/ORM/Repository/DefaultRepositoryFactory.php on line 55
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, $this->getEntityClass());
    }

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
