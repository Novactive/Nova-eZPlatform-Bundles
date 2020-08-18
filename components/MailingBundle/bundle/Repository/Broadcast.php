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

/**
 * Class Broadcast.
 */
class Broadcast extends EntityRepository
{
    /**
     * {@inheritdoc}
     */
    protected function getAlias(): string
    {
        return 'broadcast';
    }

    public function findLastBroadcasts(int $limit = 4): array
    {
        $qb = $this->createQueryBuilderForFilters([]);
        $qb->where("{$this->getAlias()}.emailSentCount > 0");
        $qb->setMaxResults($limit);
        $qb->orderBy("{$this->getAlias()}.ended", 'DESC');

        return $qb->getQuery()->getResult();
    }
}
