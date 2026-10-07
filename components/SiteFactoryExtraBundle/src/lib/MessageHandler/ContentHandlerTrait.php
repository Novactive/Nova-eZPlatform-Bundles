<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaSiteFactoryExtra\MessageHandler;

use DateTime;
use Exception;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Throwable;

trait ContentHandlerTrait
{
    protected Repository $repository;

    /**
     * @required
     */
    public function setRepository(Repository $repository): void
    {
        $this->repository = $repository;
    }

    /**
     * @param array<string, mixed> $fieldsByLanguages
     */
    protected function setContentFields(
        ContentType $contentType,
        ContentStruct $contentStruct,
        array $fieldsByLanguages
    ): void {
        foreach ($fieldsByLanguages as $languageCode => $fields) {
            foreach ($fields as $fieldID => $field) {
                $fieldDefinition = $contentType->getFieldDefinition($fieldID);
                if ($fieldDefinition instanceof FieldDefinition) {
                    $contentStruct->setField($fieldID, $field, $languageCode);
                }
            }
        }
    }

    /**
     * @param array<string, mixed>                   $fields
     * @param array<string|int, int|string|Location> $parentLocationIdList
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Content
     */
    public function importContent(
        string $contentRemoteId,
        array $fields = [],
        string $mainLanguageCode = 'eng-GB',
        ?int $ownerId = null,
        ?string $contentTypeIdentifier = null,
        array $parentLocationIdList = [2],
        ?int $sectionId = null,
        int|DateTime|null $modificationDate = null,
        bool $allowUpdate = true
    ) {
        $remoteId = $contentRemoteId;
        if (null === $ownerId) {
            $ownerId = $this->repository
                ->getPermissionResolver()
                ->getCurrentUserReference()
                ->getUserId();
        }

        try {
            try {
                $content = $this->repository->getContentService()->loadContentByRemoteId(
                    $contentRemoteId
                );
                if (!$allowUpdate) {
                    return $content;
                }

                return $this->updateContent(
                    $content,
                    $fields,
                    $parentLocationIdList,
                    $ownerId,
                    $mainLanguageCode
                );
            } catch (NotFoundException $exception) {
                return $this->createContent(
                    $contentTypeIdentifier,
                    $parentLocationIdList,
                    $fields,
                    $remoteId,
                    $ownerId,
                    $mainLanguageCode,
                    $sectionId,
                    $modificationDate
                );
            }
        } catch (Throwable $exception) {
            throw $exception;
        }
    }

    /**
     * @param array<string|int, int|string|Location> $parentLocationIdList
     * @param array<string, mixed>                   $fieldsByLanguages
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    protected function createContent(
        string $contentTypeIdentifier,
        array $parentLocationIdList,
        array $fieldsByLanguages,
        string $remoteId,
        ?int $ownerId = null,
        string $languageCode = 'eng-GB',
        ?int $sectionId = null,
        $modificationDate = null,
        bool $hidden = false,
        ?bool $alwaysAvailable = null,
    ): Content {
        $contentType = $this->repository->getContentTypeService()->loadContentTypeByIdentifier(
            $contentTypeIdentifier
        );

        /* Creating new content create structure */
        $contentCreateStruct = $this->repository->getContentService()->newContentCreateStruct(
            $contentType,
            $languageCode
        );
        $contentCreateStruct->remoteId = $remoteId;
        $contentCreateStruct->ownerId = $ownerId;
        if (null !== $modificationDate) {
            $contentCreateStruct->modificationDate = $modificationDate instanceof DateTime ?
                $modificationDate :
                DateTime::createFromFormat('U', (string) $modificationDate);
        }

        if ($sectionId) {
            $contentCreateStruct->sectionId = $sectionId;
        }

        $contentCreateStruct->alwaysAvailable = $alwaysAvailable;

        /* Update content structure fields */
        $this->setContentFields($contentType, $contentCreateStruct, $fieldsByLanguages);

        /* Assigning the content locations */
        $locationCreateStructs = [];
        foreach ($parentLocationIdList as $locationRemoteId => $parentLocationId) {
            if (empty($parentLocationId)) {
                throw new Exception('Parent location id cannot be empty');
            }
            if ($parentLocationId instanceof Location) {
                $parentLocationId = $parentLocationId->id;
            }
            if (is_string($parentLocationId)) {
                $parentLocationId = $this->repository->getLocationService()->loadLocationByRemoteId(
                    $parentLocationId
                )->id;
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
            $locationCreateStructs[] = $locationCreateStruct;
        }

        /* Creating new draft */
        $draft = $this->repository->getContentService()->createContent(
            $contentCreateStruct,
            $locationCreateStructs
        );

        /* Publish the new content draft */
        return $this->repository->getContentService()->publishVersion($draft->versionInfo);
    }

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
    protected function updateContent(
        Content $content,
        array $fieldsByLanguages,
        array $parentLocationIdList,
        ?int $ownerId = null,
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

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Exception
     */
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
