<?php

namespace Novactive\Bundle\eZProtectedContentBundle\Services;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\ObjectStateService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup;
use Ibexa\Core\Repository\SiteAccessAware\Repository;
use Psr\Log\LoggerInterface;

class ObjectStateHelper
{
    public function __construct(
        protected readonly Repository $repository,
        protected readonly ObjectStateService $objectStateService,
        protected readonly ProtectedAccessHelper $protectedAccessHelper,
        protected readonly LoggerInterface $logger,
    ) {
    }

    public string $objectStateGroupIdentifier = 'protected_content';
    public string $objectStateEmailGroupIdentifier = 'protected_content_email';

    public string $protectedObjectStateIdentifier = 'protected';
    public string $unprotectedObjectStateIdentifier = 'unprotected';

    protected function getObjectStateGroup(string $objectStateGroupIdentifier): ?ObjectStateGroup
    {
        try {
            return $this->objectStateService->loadObjectStateGroupByIdentifier($objectStateGroupIdentifier);
        } catch (NotFoundException $notFoundException) {
            $this->logger->error($notFoundException->getMessage(), [
                '$objectStateGroupIdentifier' => $objectStateGroupIdentifier,
            ]);

            return null;
        }
    }

    protected function getObjectState(ObjectStateGroup $objectStateGroup, $objectStateIdentifier): ?ObjectState
    {
        try {
            return $this->objectStateService->loadObjectStateByIdentifier($objectStateGroup, $objectStateIdentifier);
        } catch (NotFoundException $notFoundException) {
            $this->logger->error($notFoundException->getMessage(), [
                '$objectStateGroupIdentifier' => $objectStateGroup->identifier,
                '$objectStateIdentifier' => $objectStateIdentifier,
            ]);
        }

        return null;
    }

    public function setStatesForContentAndDescendants(Content $content, bool $forceSubtreeProcessing = false): void
    {
        // On met tout de suite à jour les state pour $content.
        $this->setStatesForContent($content);

        if (!$forceSubtreeProcessing && !$this->protectedAccessHelper->hasInheritableProtectedAccess($content)) {
            // Le contenu n'a aucune protection "héritable". On laisse ses descendant tranquil.
            return;
        }

        $locations = $this->repository->getLocationService()->loadLocations($content->contentInfo);
        $subtrees = array_map(function (Location $location) {
            return $location->getPathString();
        }, $locations);

        $query = new LocationQuery();
        $query->filter = new Query\Criterion\LogicalAnd([
            new Query\Criterion\Subtree($subtrees),
        ]);
        $query->limit = 25;

        $searchResult = $this->repository->getSearchService()->findLocations($query);
        if ($searchResult->totalCount > $query->limit) {
            $this->logger->error('Too many locations found for content', [
                'contentId' => $content->id,
                'totalCount' => $searchResult->totalCount,
            ]);
        }
        foreach ($searchResult->searchHits as $hit) {
            /** @var Location $location */
            $location = $hit->valueObject;
            $this->setStatesForContent($location->getContent());
        }
    }

    public function setStatesForContent(Content $content): void
    {
        $data = [
            $this->objectStateGroupIdentifier => $this->protectedAccessHelper->hasProtectedAccess($content),
            $this->objectStateEmailGroupIdentifier => $this->protectedAccessHelper->hasEmailProtectedAccess($content),
        ];

        foreach ($data as $objectStateGroupIdentifier => $isProtected) {
            $objectStateGroup = $this->getObjectStateGroup($objectStateGroupIdentifier);
            if ($objectStateGroup) {
                $objectState = null;
                if ($isProtected) {
                    $objectState = $this->getObjectState($objectStateGroup, $this->protectedObjectStateIdentifier);
                } else {
                    $objectState = $this->getObjectState($objectStateGroup, $this->unprotectedObjectStateIdentifier);
                }
                if ($objectState) {
                    $contentState = $this->objectStateService->getContentState(
                        $content->contentInfo,
                        $objectStateGroup
                    );
                    if ($contentState->identifier !== $objectState->identifier) {
                        $this->repository->sudo(function () use ($content, $objectStateGroup, $objectState) {
                            $this->objectStateService->setContentState(
                                $content->contentInfo,
                                $objectStateGroup,
                                $objectState
                            );
                        });
                    }
                }
            }
        }
    }
}
