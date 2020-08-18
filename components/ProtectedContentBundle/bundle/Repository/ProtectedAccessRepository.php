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

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use Novactive\Bundle\eZProtectedContentBundle\Entity\ProtectedAccess;

class ProtectedAccessRepository extends EntityRepository
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @required
     */
    public function setRepository(Repository $repository): void
    {
        $this->repository = $repository;
    }

    protected function getAlias(): string
    {
        return 'pa';
    }

    protected function getEntityClass(): string
    {
        return ProtectedAccess::class;
    }

    public function findByContent(Content $content): array
    {
        $contentIds = $this->repository->sudo(
            function (Repository $repository) use ($content) {
                $ids = [$content->id];
                $locations = $repository->getLocationService()->loadLocations($content->contentInfo);
                foreach ($locations as $location) {
                    /** @var Location $location */
                    $parent = $repository->getLocationService()->loadLocation($location->parentLocationId);
                    $ids[] = $parent->contentInfo->id;
                }

                return $ids;
            }
        );

        $qb = parent::createQueryBuilderForFilters();
        $qb->where($qb->expr()->eq($this->getAlias().'.enabled', true));
        $qb->andWhere(
            $qb->expr()->in($this->getAlias().'.contentId', ':contentIds')
        );
        $qb->setParameter('contentIds', $contentIds);
        $results = $qb->getQuery()->getResult();
        $filteredResults = [];
        foreach ($results as $protection) {
            /** @var ProtectedAccess $protection */
            if ($protection->getContentId() === $content->id) {
                $filteredResults[] = $protection;
                continue;
            }
            /** @var ProtectedAccess $protection */
            if (true === $protection->isProtectChildren()) {
                $filteredResults[] = $protection;
                continue;
            }
        }

        return $filteredResults;
    }
}
