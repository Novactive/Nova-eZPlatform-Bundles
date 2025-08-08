<?php

namespace Novactive\Bundle\eZProtectedContentBundle\Services;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Core\Repository\Repository;
use Novactive\Bundle\eZProtectedContentBundle\Entity\ProtectedAccess;
use Novactive\Bundle\eZProtectedContentBundle\Repository\ProtectedAccessRepository;
use Psr\Log\LoggerInterface;

class ProtectedAccessHelper
{
    public function __construct(
        protected readonly ProtectedAccessRepository $protectedAccessRepository,
        protected readonly Repository $repository,
        protected readonly LoggerInterface $logger,
    ) { }

    /**
     * Retourne toutes les protections qui s'appliquent au contenu. En prenant en compte ses ancêtres et ses multiples emplacements.
     * @param Content $content
     * @return ProtectedAccess[]
     */
    public function getProtectedAccessList(Content $content): array
    {
        return $this->protectedAccessRepository->findByContent($content);
    }

    public function hasProtectedAccess(Content $content): bool
    {
        return !!$this->getProtectedAccessList($content);
    }

    /** Est-ce que le contenu a des protections héritables ?  */
    public function hasInheritableProtectedAccess(Content $content): bool
    {
        $protectedAccessList = $this->getProtectedAccessList($content);
        return !!array_filter($protectedAccessList, function (ProtectedAccess $protectedAccess) {
            return $protectedAccess->isEnabled() && $protectedAccess->isProtectChildren();
        });
    }


    public function hasPasswordProtectedAccess(Content $content): bool
    {
        $protectedAccessList = $this->getProtectedAccessList($content);
        return !!array_filter($protectedAccessList, function (ProtectedAccess $protectedAccess) {
            return $protectedAccess->isEnabled() && $protectedAccess->getPassword();
        });
    }

    public function hasEmailProtectedAccess(Content $content): bool
    {
        $protectedAccessList = $this->getProtectedAccessList($content);
        return !!array_filter($protectedAccessList, function (ProtectedAccess $protectedAccess) {
            return $protectedAccess->isEnabled() && $protectedAccess->getAsEmail();
        });
    }

    /**
     * Retourne le nombre de contenus impactés par la protection.
     * @param ProtectedAccess $protectedAccess
     * @return int
     */
    public function count(ProtectedAccess $protectedAccess): int
    {
        $content = $this->getContent($protectedAccess);
        if (!$content) {
            return 0;
        }

        if ($protectedAccess->isProtectChildren()) {
            return 1;
        }

        $query = new LocationQuery();
        $query->filter = $this->getSubtreeCriterion($content);
        $query->limit = 0;
        return $this->repository->getSearchService()->findContent($query)->totalCount;
    }

    /**
     * Retourne le contenu sur le quel s'applique la protection.
     * @param ProtectedAccess $protectedAccess
     * @return Content|null
     */
    public function getContent(ProtectedAccess $protectedAccess): ?Content
    {
        try {
            return $this->repository->getContentService()->loadContent($protectedAccess->getContentId());
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), [
                __METHOD__,
                'ProtectedAccess ID' => $protectedAccess->getId(),
            ]);
            return null;
        }
    }

    public function getSubtreeCriterion(Content $content): Query\Criterion\Subtree
    {
        $contentInfo = $content->contentInfo;
        $locations = $this->repository->getLocationService()->loadLocations($contentInfo);
        $subtrees = array_map(function (Location $location) {
            return $location->getPathString();
        }, $locations);

        return new Query\Criterion\Subtree($subtrees);
    }
}
