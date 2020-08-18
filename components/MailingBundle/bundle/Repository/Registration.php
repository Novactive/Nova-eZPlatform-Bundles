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
 * Class Registration.
 */
class Registration extends EntityRepository
{
    /**
     * {@inheritdoc}
     */
    protected function getAlias(): string
    {
        return 'reg';
    }

    public function createQueryBuilderForFilters(array $filters = []): QueryBuilder
    {
        $qb = parent::createQueryBuilderForFilters($filters);
        $qb
            ->innerJoin('reg.user', 'user', Join::WITH, $qb->expr()->eq('user.restricted', ':restricted'))
            ->setParameter('restricted', false);

        $mailingLists = null;
        if (isset($filters['campaign'])) {
            /** @var CampaignEntity $campaign */
            $campaign = $filters['campaign'];
            $mailingLists = $campaign->getMailingLists();
        }
        if (isset($filters['mailingLists'])) {
            $mailingLists = $filters['mailingLists'];
        }
        if (null !== $mailingLists) {
            $qb->andWhere($qb->expr()->in('reg.mailingList', ':mailinglists'))->setParameter(
                'mailinglists',
                $mailingLists
            );
        }
        if (isset($filters['isApproved'])) {
            $qb->andWhere($qb->expr()->in('reg.approved', ':approved'))->setParameter(
                'approved',
                $filters['isApproved']
            );
        }

        return $qb;
    }
}
