<?php

namespace Novactive\Bundle\eZProtectedContentBundle\Repository;

use DateTime;
use Novactive\Bundle\eZProtectedContentBundle\Entity\ProtectedTokenStorage;

class ProtectedTokenStorageRepository extends EntityRepository
{
    protected function getAlias(): string
    {
        return 'pts';
    }

    protected function getEntityClass(): string
    {
        return ProtectedTokenStorage::class;
    }

    public function findUnexpiredBy(array $criteria = []): array
    {
        $dbQuery = $this->_em->createQueryBuilder()
            ->select('c')
            ->from(ProtectedTokenStorage::class, 'c')
            ->where('c.created >= :nowMinusOneHour')
            ->setParameter('nowMinusOneHour', new DateTime('now - 1 hours'));

        foreach ($criteria as $key => $criterion) {
            $dbQuery->andWhere("c.$key = '$criterion'");
        }

        return $dbQuery->getQuery()->getResult();
    }

    public function findExpired(): array
    {
        $dbQuery = $this->_em->createQueryBuilder()
            ->select('c')
            ->from(ProtectedTokenStorage::class, 'c')
            ->where('c.created < :nowMinusOneHour')
            ->setParameter('nowMinusOneHour', new DateTime('now - 1 hours'));

        return $dbQuery->getQuery()->getResult();
    }
}
