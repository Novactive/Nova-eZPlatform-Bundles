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
use Novactive\Bundle\eZMailingBundle\Entity\User as UserEntity;

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
            $joinExpr = $qb->expr()->andX(
                $qb->expr()->in('reg.mailingList', ':mailingLists')
            );
            if (isset($filters['isApproved'])) {
                $joinExpr->add($qb->expr()->eq('reg.approved', ':approved'));
            }
            $qb
                ->innerJoin(
                    'u.registrations',
                    'reg',
                    Join::WITH,
                    $joinExpr
                )->setParameter('mailingLists', $mailingLists);

            if (isset($filters['isApproved'])) {
                $qb->setParameter('approved', $filters['isApproved']);
            }
        }

        if (isset($filters['status'])) {
            $qb->andWhere($qb->expr()->in('u.status', ':statuses'))->setParameter('statuses', $filters['status']);
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

    /**
     * @param $mailingLists
     *
     * @return array|\Doctrine\Common\Collections\ArrayCollection
     */
    public function findValidRecipients($mailingLists)
    {
        return $this->findByFilters(
            [
                'mailingLists' => $mailingLists,
                'isApproved'   => true,
                'status'       => [UserEntity::CONFIRMED, UserEntity::SOFT_BOUNCE],
            ]
        );
    }

    /**
     * @param $mailingLists
     *
     * @return int
     */
    public function countValidRecipients($mailingLists): int
    {
        return $this->countByFilters(
            [
                'mailingLists' => $mailingLists,
                'isApproved'   => true,
                'status'       => [UserEntity::CONFIRMED, UserEntity::SOFT_BOUNCE],
            ]
        );
    }

    /**
     * @param int $limit
     *
     * @return array
     */
    public function findLastUpdated(int $limit = 10): array
    {
        $qb = $this->createQueryBuilderForFilters([]);
        $qb->setMaxResults($limit);
        $qb->orderBy("{$this->getAlias()}.updated", 'DESC');

        return $qb->getQuery()->getResult();
    }
}
