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
use Symfony\Bridge\Doctrine\RegistryInterface;

abstract class EntityRepository extends ServiceEntityRepository
{
    abstract protected function getAlias(): string;

    public function __construct(RegistryInterface $registry)
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
