<?php

namespace Novactive\Bundle\eZProtectedContentBundle\Services;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\Repository\Repository;
use Novactive\Bundle\eZProtectedContentBundle\Entity\ProtectedAccess;
use Novactive\Bundle\eZProtectedContentBundle\Repository\ProtectedAccessRepository;
use Psr\Log\LoggerInterface;

readonly class ProtectedAccessHelper
{
    public function __construct(
        protected ProtectedAccessRepository $protectedAccessRepository,
        protected Repository                $repository,
        protected LoggerInterface           $logger,
    ) {
    }

    /**
     * Retourne toutes les protections qui affectent ce contenu.
     * Que ce soit directement, ou via ses ancêtres.
     * En prenant en compte ses multiples emplacements.
     *
     * @return ProtectedAccess[]
     */
    public function getProtectedAccessList(Content $content): array
    {
        return $this->protectedAccessRepository->findByContent($content);
    }

    public function hasProtectedAccess(Content $content): bool
    {
        return (bool) $this->getProtectedAccessList($content);
    }

    /** Est-ce que le contenu a des protections héritables ?  */
    public function hasInheritableProtectedAccess(Content $content): bool
    {
        $protectedAccessList = $this->getProtectedAccessList($content);

        return (bool) array_filter($protectedAccessList, function (ProtectedAccess $protectedAccess) {
            return $protectedAccess->isEnabled() && $protectedAccess->isProtectChildren();
        });
    }

    //    public function hasPasswordProtectedAccess(Content $content): bool
    //    {
    //        $protectedAccessList = $this->getProtectedAccessList($content);
    //        return !!array_filter($protectedAccessList, function (ProtectedAccess $protectedAccess) {
    //            return $protectedAccess->isEnabled() && $protectedAccess->getPassword();
    //        });
    //    }

    public function hasEmailProtectedAccess(Content $content): bool
    {
        $protectedAccessList = $this->getProtectedAccessList($content);

        return (bool) array_filter($protectedAccessList, function (ProtectedAccess $protectedAccess) {
            return $protectedAccess->isEnabled() && $protectedAccess->getAsEmail();
        });
    }

    /**
     * Retourne le nombre de contenus impactés par la protection.
     */
    public function count(ProtectedAccess $protectedAccess): int
    {
        $content = $this->getContent($protectedAccess);
        if (!$content) {
            return 0;
        }

        if (!$protectedAccess->isProtectChildren()) {
            return 1;
        }

        $query = new LocationQuery();
        $query->filter = $this->getSubtreeCriterion($content);
        $query->limit = 0;

        return $this->repository->getSearchService()->findContent($query)->totalCount;
    }

    /**
     * Retourne le contenu sur le quel s'applique la protection.
     */
    public function getContent(ProtectedAccess $protectedAccess): ?Content
    {
        $contentId = $protectedAccess->getContentId();
        try {
            return $this->repository->getContentService()->loadContent($contentId);
        } catch (NotFoundException) {
            $this->logger->debug("Could not find 'Content' with id $contentId", [
                __METHOD__,
                'ProtectedAccess ID' => $protectedAccess->getId(),
                'ContentId' => $contentId,
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
