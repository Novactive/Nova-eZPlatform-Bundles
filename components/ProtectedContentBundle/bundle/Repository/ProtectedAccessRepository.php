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

use Doctrine\ORM\EntityManagerInterface;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Novactive\Bundle\eZProtectedContentBundle\Entity\ProtectedAccess;

class ProtectedAccessRepository
{
    public function __construct(
        protected readonly Repository $repository,
        protected readonly EntityManagerInterface $entityManager,
    ) {
    }

    protected function getAlias(): string
    {
        return 'pa';
    }

    protected function getEntityClass(): string
    {
        return ProtectedAccess::class;
    }

    public function findByContent(?Content $content): array
    {
        if (null === $content) {
            return [];
        }
        $contentIds = $this->getContentIds($content);

        $entityRepository = $this->entityManager->getRepository($this->getEntityClass());

        $qb = $entityRepository->createQueryBuilder($this->getAlias());
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

    /**
     * Retourne les ContentID du contenu et de tous ces descendants en prenant en compte ses multiples emplacements.
     */
    protected function getContentIds(Content $content): array
    {
        return $this->repository->sudo(
            function (Repository $repository) use ($content) {
                $ids = [$content->id];
                $locations = $repository->getLocationService()->loadLocations($content->contentInfo);
                $ct = 0;
                foreach ($locations as $location) {
                    /** @var Location $loc */
                    $loc = $location;
                    while (
                        $loc->parentLocationId
                        && ($loc = $repository->getLocationService()->loadLocation($loc->parentLocationId))
                        && $loc instanceof Location
                        && $loc->parentLocationId
                        && 1 !== $loc->parentLocationId
                    ) {
                        ++$ct;
                        $ids[] = $loc->getContentInfo()->id;
                        if ($ct >= 15) {
                            break 2;
                        }
                    }
                }

                return $ids;
            }
        );
    }
}
