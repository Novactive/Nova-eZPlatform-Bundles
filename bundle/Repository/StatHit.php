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

/**
 * Class StatHit.
 */
class StatHit extends EntityRepository
{
    /**
     * {@inheritdoc}
     */
    protected function getAlias(): string
    {
        return 'stathit';
    }

    /**
     * @param array $filters
     *
     * @return QueryBuilder
     */
    public function createQueryBuilderForFilters(array $filters = []): QueryBuilder
    {
        $qb = parent::createQueryBuilderForFilters($filters);
        if (isset($filters['mailings'])) {
            $mailings = $filters['mailings'];
        }
        if (null !== $mailings) {
            $qb->andWhere($qb->expr()->in('stathit.mailing', ':mailings'))->setParameter(
                'mailings',
                $mailings
            );
        }

        return $qb;
    }

    /**
     * @param Mailing[] $mailings
     *
     * @return array
     */
    public function getBrowserMapCount($mailings): array
    {
        $qb = $this->createQueryBuilderForFilters(['mailings' => $mailings]);
        $qb->select($qb->expr()->count($this->getAlias().'.id').' as nb', $this->getAlias().'.browserName');
        $qb->groupBy($this->getAlias().'.userKey', $this->getAlias().'.browserName');
        $results       = $qb->getQuery()->getArrayResult();
        $mappedResults = [];

        foreach ($results as $result) {
            $mappedResults[$result['browserName']] = (int) $result['nb'];
        }

        return $mappedResults;
    }

    /**
     * @param Mailing[] $mailings
     *
     * @return array
     */
    public function getOSMapCount($mailings): array
    {
        $qb = $this->createQueryBuilderForFilters(['mailings' => $mailings]);
        $qb->select($qb->expr()->count($this->getAlias().'.id').' as nb', $this->getAlias().'.osName');
        $qb->groupBy($this->getAlias().'.userKey', $this->getAlias().'.osName');
        $results       = $qb->getQuery()->getArrayResult();
        $mappedResults = [];

        foreach ($results as $result) {
            $mappedResults[$result['osName']] = (int) $result['nb'];
        }

        return $mappedResults;
    }

    /**
     * @param Mailing[] $mailings
     *
     * @return array
     */
    public function getURLMapCount($mailings): array
    {
        $qb = $this->createQueryBuilderForFilters(['mailings' => $mailings]);
        $qb->select($qb->expr()->count($this->getAlias().'.id').' as nb', $this->getAlias().'.url');
        $qb->andWhere($qb->expr()->notIn($this->getAlias().'.url', ':url'))->setParameter('url', '-');
        $qb->groupBy($this->getAlias().'.userKey', $this->getAlias().'.url');
        $results       = $qb->getQuery()->getArrayResult();
        $mappedResults = [];

        foreach ($results as $result) {
            $mappedResults[$result['url']] = (int) $result['nb'];
        }

        return $mappedResults;
    }

    /**
     * @param Mailing[] $mailings
     *
     * @return array
     */
    public function getOpenedCount($mailings): int
    {
        $qb = $this->createQueryBuilderForFilters(['mailings' => $mailings]);
        $qb->select($qb->expr()->count($this->getAlias().'.id').' as nb');
        $qb->andWhere($qb->expr()->eq($this->getAlias().'.url', ':url'))->setParameter('url', '-');
        $qb->groupBy($this->getAlias().'.userKey');

        return (int) ($qb->getQuery()->getOneOrNullResult()['nb'] ?? 0);
    }
}
