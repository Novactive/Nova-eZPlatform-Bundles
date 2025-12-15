<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Content;

use AlmaviaCX\Bundle\IbexaImportExport\Writer\Utils\Checksum;
use Exception;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;

class IbexaContentUpdater extends AbstractIbexaContentHandler
{
    /**
     * @param array<string, mixed>                   $fieldsByLanguages
     * @param array<int|string, Location|string|int> $parentLocationIdList
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
        Checksum $checksum,
        int $ownerId = null,
        string $mainLanguageCode = 'eng-GB',
        bool|null $hidden = null,
        bool $allowMove = false
    ): Content {
        $doUpdate = $this->doContentNeedUpdate($content, $checksum);
        if ($doUpdate) {
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
            $content = $this->repository->getContentService()->publishVersion($contentDraft->versionInfo);

            $this->saveContentChecksum($content, $checksum);
        }

        if ($allowMove) {
            $this->handleLocations($content, $parentLocationIdList, $hidden);
        } elseif (null !== $hidden) {
            $this->handleLocationsVisibility($content, $hidden);
        }

        return $content;
    }

    /**
     * @param array<int|string, Location|string|int> $parentLocationIdList
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    protected function handleLocations(Content $content, array $parentLocationIdList, bool|null $hidden): void
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

    /**
     * @param Location[] $existingLocations
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    protected function handleLocation(
        Content $content,
        Location|int|string $parentLocationId,
        int|string $locationRemoteId,
        array $existingLocations,
        bool|null $hidden
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
                if (null !== $hidden && $existingLocation->hidden !== $hidden) {
                    if ($hidden) {
                        $this->repository->getLocationService()->hideLocation($existingLocation);
                    } else {
                        $this->repository->getLocationService()->unhideLocation($existingLocation);
                    }
                }

                return $existingLocation;
            }
        }

        $locationCreateStruct = $this->repository->getLocationService()->newLocationCreateStruct(
            $parentLocationId
        );
        if (is_string($locationRemoteId)) {
            $locationCreateStruct->remoteId = $locationRemoteId;
        }
        if (true === $hidden) {
            $locationCreateStruct->hidden = true;
        }

        return $this->repository->getLocationService()->createLocation(
            $content->contentInfo,
            $locationCreateStruct
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    protected function handleLocationsVisibility(Content $content, bool $hidden): void
    {
        $existingLocations = $this->repository->getLocationService()->loadLocations($content->contentInfo);
        foreach ($existingLocations as $existingLocation) {
            if ($existingLocation->hidden !== $hidden) {
                if ($hidden) {
                    $this->repository->getLocationService()->hideLocation($existingLocation);
                } else {
                    $this->repository->getLocationService()->unhideLocation($existingLocation);
                }
            }
        }
    }
}
