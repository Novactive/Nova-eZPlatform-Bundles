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

use Doctrine\ORM\QueryBuilder;

class Mailing extends EntityRepository
{
    protected function getAlias(): string
    {
        return 'mail';
    }

    public function createQueryBuilderForFilters(
        array $filters = [],
        ?string $orderBy = null,
        ?int $limit = null
    ): QueryBuilder {
        $qb = parent::createQueryBuilderForFilters($filters);
        if (isset($filters['campaign'])) {
            $qb->andWhere($qb->expr()->eq('mail.campaign', ':campaign'))->setParameter(
                'campaign',
                $filters['campaign']
            );
        }

        if (isset($filters['status'])) {
            $qb->andWhere($qb->expr()->in('mail.status', ':statuses'))->setParameter('statuses', $filters['status']);
        }

        return $qb;
    }

    public function findLastUpdated(int $limit = 10): array
    {
        $qb = $this->createQueryBuilderForFilters([]);
        $qb->setMaxResults($limit);
        $qb->orderBy("{$this->getAlias()}.updated", 'DESC');

        return $qb->getQuery()->getResult();
    }
}
