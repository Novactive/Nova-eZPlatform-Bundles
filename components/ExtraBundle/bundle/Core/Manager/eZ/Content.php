<?php

/**
 * NovaeZExtraBundle Content Manager.
 *
 * @package   Novactive\Bundle\eZExtraBundle
 *
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZExtraBundle\Core\Manager\eZ;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content as ValueContent;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType as eZContentType;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;

class Content
{
    /**
     * @var Repository
     */
    private $eZPublishRepository;

    public function __construct(Repository $api)
    {
        $this->eZPublishRepository = $api;
    }

    public function getRepository(): Repository
    {
        return $this->eZPublishRepository;
    }

    public function getContentService(): ContentService
    {
        return $this->eZPublishRepository->getContentService();
    }

    public function getContentTypeService(): ContentTypeService
    {
        return $this->eZPublishRepository->getContentTypeService();
    }

    public function getLocationService(): LocationService
    {
        return $this->eZPublishRepository->getLocationService();
    }

    /**
     * Change the user of the repository
     * Note: you need to keep the current user if you want to go back on the current user.
     */
    public function sudoRoot(): void
    {
        $this->eZPublishRepository->getPermissionResolver()->setCurrentUserReference(
            $this->eZPublishRepository->getUserService()->loadUser(14)
        );
    }

    public function createContent(
        string $contentTypeIdentifier,
        int $parentLocationId,
        array $data,
        array $options = [],
        string $lang = 'eng-GB'
    ): ValueContent {
        $contentService = $this->getContentService();
        $contentType = $this->getContentTypeService()->loadContentTypeByIdentifier($contentTypeIdentifier);
        $contentCreateStruct = $contentService->newContentCreateStruct($contentType, $lang);

        if (!empty($options['remoteId'])) {
            $contentCreateStruct->remoteId = $options['remoteId'];
        }

        if (!empty($options['sectionId'])) {
            $contentCreateStruct->sectionId = $options['sectionId'];
        }

        if (!empty($options['modified'])) {
            $contentCreateStruct->modificationDate = $options['modified'];
        }

        if (!empty($options['alwaysAvailable'])) {
            $contentCreateStruct->alwaysAvailable = $options['alwaysAvailable'];
        }

        $this->autoFillStruct(
            $this->getContentTypeService()->loadContentTypeByIdentifier($contentTypeIdentifier),
            $contentCreateStruct,
            $data
        );

        $locationCreateStruct = $this->getLocationService()->newLocationCreateStruct($parentLocationId);

        if (!empty($options['priority'])) {
            $locationCreateStruct->priority = $options['priority'];
        }

        $draft = $contentService->createContent($contentCreateStruct, [$locationCreateStruct]);

        return $this->publishVersion($draft, $options);
    }

    public function updateContent(
        ValueContent $content,
        array $data,
        array $options = [],
        string $lang = 'eng-GB'
    ): ValueContent {
        $contentService = $this->getContentService();
        $contentDraft = $contentService->createContentDraft($content->contentInfo);
        $contentUpdateStruct = $contentService->newContentUpdateStruct();
        $contentUpdateStruct->initialLanguageCode = $lang;

        $this->autoFillStruct(
            $this->getContentTypeService()->loadContentType($content->contentInfo->contentTypeId),
            $contentUpdateStruct,
            $data
        );

        $contentDraft = $contentService->updateContent($contentDraft->versionInfo, $contentUpdateStruct);

        return $this->publishVersion($contentDraft, $options);
    }

    protected function publishVersion(ValueContent $draft, array $options = []): ValueContent
    {
        if (
            (\array_key_exists('callback_before_publish', $options)) &&
            (\is_callable($options['callback_before_publish']))
        ) {
            $contentService = $this->getContentService();
            $contentUpdateStruct = $contentService->newContentUpdateStruct();
            $options['callback_before_publish']($draft, $contentUpdateStruct);
            $draft = $contentService->updateContent($draft->versionInfo, $contentUpdateStruct);
        }

        $content = $this->getContentService()->publishVersion($draft->versionInfo);

        if (count($options) > 0) {
            $doUpdate = false;
            $contentService = $this->getContentService();
            $metadataUpdate = $contentService->newContentMetadataUpdateStruct();
            if (!empty($options['created'])) {
                $metadataUpdate->publishedDate = $options['created'];
                if (
                    $content->contentInfo->publishedDate->getTimestamp() !==
                    $metadataUpdate->publishedDate->getTimestamp()
                ) {
                    $doUpdate = true;
                }
            }

            if (!empty($options['modified'])) {
                $metadataUpdate->modificationDate = $options['modified'];
                if (
                    $content->contentInfo->modificationDate->getTimestamp() !==
                    $metadataUpdate->modificationDate->getTimestamp()
                ) {
                    $doUpdate = true;
                }
            }
            if (true === $doUpdate) {
                $contentService->updateContentMetadata($content->contentInfo, $metadataUpdate);
            }
        }

        return $content;
    }

    protected function autoFillStruct(eZContentType $contentType, ValueObject $contentStruct, array $data): void
    {
        /* @var ContentUpdateStruct|ContentUpdateStruct $contentStruct */

        foreach ($contentType->getFieldDefinitions() as $field) {
            /** @var FieldDefinition $field */
            $fieldName = $field->identifier;
            if (!\array_key_exists($fieldName, $data)) {
                continue;
            }
            $fieldValue = $data[$fieldName];
            $contentStruct->setField($fieldName, $fieldValue);
        }
    }

    public function createUpdateContent(
        string $contentTypeIdentifier,
        int $parentLocationId,
        array $data,
        string $remoteId,
        array $options = [],
        string $lang = 'eng-GB'
    ): ValueContent {
        $options['remoteId'] = $remoteId;
        try {
            $content = $this->getContentService()->loadContentByRemoteId($remoteId);
            if ((\array_key_exists('do_no_update', $options)) && (true == $options['do_no_update'])) {
                return $content;
            }
            $newContent = $this->updateContent($content, $data, $options, $lang);
            if ((\array_key_exists('callback_update', $options)) && (\is_callable($options['callback_update']))) {
                $options['callback_update']($newContent);
            }
        } catch (NotFoundException $e) {
            $newContent = $this->createContent($contentTypeIdentifier, $parentLocationId, $data, $options, $lang);
            if ((\array_key_exists('callback_create', $options)) && (\is_callable($options['callback_create']))) {
                $options['callback_create']($newContent);
            }
        }

        return $newContent;
    }

    public function getCreateContent(
        string $contentTypeIdentifier,
        int $parentLocationId,
        array $data,
        string $remoteId,
        array $options = [],
        string $lang = 'eng-GB'
    ): ValueContent {
        $options['do_no_update'] = true;

        return $this->createUpdateContent(
            $contentTypeIdentifier,
            $parentLocationId,
            $data,
            $remoteId,
            $options,
            $lang
        );
    }

    public function addLocation(ValueContent $content, array $destinationLocationIds = []): void
    {
        $existingLocations = $this->getLocationService()->loadLocations($content->contentInfo);
        $existingLocationsIds = [];
        foreach ($existingLocations as $existingLocation) {
            $existingLocationsIds[] = $existingLocation->parentLocationId;
        }

        foreach ($destinationLocationIds as $destinationLocationId) {
            if (!\in_array($destinationLocationId, $existingLocationsIds, true)) {
                $locationCreateStruct = $this->getLocationService()->newLocationCreateStruct($destinationLocationId);
                $this->getLocationService()->createLocation($content->contentInfo, $locationCreateStruct);
            }
        }
    }
}
