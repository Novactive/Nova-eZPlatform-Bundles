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

class MailingList extends EntityRepository
{
    protected function getAlias(): string
    {
        return 'ml';
    }

    public function createQueryBuilderForFilters(array $filters = []): QueryBuilder
    {
        $qb = parent::createQueryBuilderForFilters($filters);
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
