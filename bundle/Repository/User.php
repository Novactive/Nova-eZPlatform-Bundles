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

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Novactive\Bundle\eZMailingBundle\Entity\Campaign as CampaignEntity;

/**
 * Class User.
 */
class User extends EntityRepository
{
    /**
     * {@inheritdoc}
     */
    protected function getAlias(): string
    {
        return 'u';
    }

    /**
     * @param array $filters
     *
     * @return QueryBuilder
     */
    public function createQueryBuilderForFilters(array $filters = []): QueryBuilder
    {
        $qb = parent::createQueryBuilderForFilters($filters);
        $qb->where($qb->expr()->eq('u.restricted', ':restricted'))->setParameter('restricted', false);

        $mailingLists = null;
        if (isset($filters['campaign'])) {
            /** @var CampaignEntity $campaign */
            $campaign     = $filters['campaign'];
            $mailingLists = $campaign->getMailingLists();
        }
        if (isset($filters['mailingLists'])) {
            $mailingLists = $filters['mailingLists'];
        }
        if (null !== $mailingLists) {
            $qb
                ->innerJoin(
                    'u.registrations',
                    'reg',
                    Join::WITH,
                    $qb->expr()->in('reg.mailingList', ':mailingLists')
                )
                ->setParameter('mailingLists', $mailingLists);
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
