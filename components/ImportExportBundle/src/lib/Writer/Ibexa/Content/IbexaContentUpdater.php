<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Content;

use Exception;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;

class IbexaContentUpdater extends AbstractIbexaContentHandler
{
    /**
     * @param array<string, mixed> $fieldsByLanguages
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function __invoke(
        Content $content,
        array $fieldsByLanguages,
        array $parentLocationIdList,
        int $ownerId = null,
        string $mainLanguageCode = 'eng-GB',
        bool $hidden = false
    ): Content {
        $contentType = $this->repository->getContentTypeService()->loadContentType(
            $content->contentInfo->contentTypeId
        );

        $contentInfo = $content->contentInfo;
        $contentDraft = $this->repository->getContentService()->createContentDraft($contentInfo);

        /* Creating new content update structure */
        $contentUpdateStruct = $this->repository
            ->getContentService()
            ->newContentUpdateStruct();
        $contentUpdateStruct->initialLanguageCode = $mainLanguageCode; // set language for new version
        $contentUpdateStruct->creatorId = $ownerId;

        $this->setContentFields(
            $contentType,
            $contentUpdateStruct,
            $fieldsByLanguages,
        );

        $contentDraft = $this->repository->getContentService()->updateContent(
            $contentDraft->versionInfo,
            $contentUpdateStruct
        );

        /* Publish the new content draft */
        $publishedContent = $this->repository->getContentService()->publishVersion($contentDraft->versionInfo);

        $this->handleLocations($content, $parentLocationIdList, $hidden);

        return $publishedContent;
    }

    protected function handleLocations(Content $content, array $parentLocationIdList, bool $hidden): void
    {
        $existingLocations = $this->repository->getLocationService()->loadLocations($content->contentInfo);
        $locationsToKeep = [];
        foreach ($parentLocationIdList as $locationRemoteId => $parentLocationId) {
            if (empty($parentLocationId)) {
                throw new Exception('Parent location id cannot be empty');
            }
            $locationsToKeep[] = $this->handleLocation(
                $content,
                $parentLocationId,
                $locationRemoteId,
                $existingLocations,
                $hidden
            );
        }

        foreach ($existingLocations as $existingLocation) {
            if (!in_array($existingLocation, $locationsToKeep)) {
                $this->repository->getLocationService()->deleteLocation($existingLocation);
            }
        }
    }

    protected function handleLocation(
        Content $content,
        $parentLocationId,
        $locationRemoteId,
        array $existingLocations,
        bool $hidden
    ): Location {
        if ($parentLocationId instanceof Location) {
            $parentLocationId = $parentLocationId->id;
        }
        if (is_string($parentLocationId)) {
            $parentLocationId = $this->repository->getLocationService()->loadLocationByRemoteId(
                $parentLocationId
            )->id;
        }

        foreach ($existingLocations as $existingLocation) {
            if ($existingLocation->parentLocationId === $parentLocationId) {
                return $existingLocation;
            }
        }

        $locationCreateStruct = $this->repository->getLocationService()->newLocationCreateStruct(
            $parentLocationId
        );
        if (is_string($locationRemoteId)) {
            $locationCreateStruct->remoteId = $locationRemoteId;
        }
        if ($hidden) {
            $locationCreateStruct->hidden = true;
        }

        return $this->repository->getLocationService()->createLocation(
            $content->contentInfo,
            $locationCreateStruct
        );
    }
}
