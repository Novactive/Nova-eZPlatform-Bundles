<?php

/**
 * NovaeZRssFeedBundle.
 *
 * @package   NovaeZRssFeedBundle
 *
 * @author    Novactive
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZRssFeedBundle/blob/master/LICENSE
 */

namespace Novactive\EzRssFeedBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Novactive\EzRssFeedBundle\Entity\RssFeeds;

/**
 * RssFeedsRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RssFeedsRepository extends EntityRepository
{
    public function findFeedBySiteIdentifierAndUrlSlug(string $siteIdentifier, string $urlSlug)
    {
        $qb = $this->createQueryBuilder('f');
        $qb->leftJoin('f.feedSites', 'fs')
            ->andWhere('fs.identifier = :siteIdentifier')
            ->orWhere('fs.identifier IS NULL')
            ->andWhere('f.urlSlug = :urlSlug')
            ->andWhere('f.status = :status')
            ->setParameter('siteIdentifier', $siteIdentifier)
            ->setParameter('urlSlug', $urlSlug)
            ->setParameter('status', RssFeeds::STATUS_ENABLED);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findFeedsBySiteIdentifier(string $siteIdentifier)
    {
        $qb = $this->createQueryBuilder('f');
        $qb->leftJoin('f.feedSites', 'fs')
            ->andWhere('fs.identifier = :siteIdentifier')
            ->orWhere('fs.identifier IS NULL')
            ->setParameter('siteIdentifier', $siteIdentifier);

        return $qb->getQuery()->getResult();
    }
}
