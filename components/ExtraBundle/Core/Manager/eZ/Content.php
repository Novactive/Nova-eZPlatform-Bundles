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

namespace Novactive\Bundle\eZExtraBundle\Core\Manager\eZ;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content as ValueContent;
use eZ\Publish\API\Repository\Values\Content\ContentUpdateStruct;
use eZ\Publish\API\Repository\Values\ContentType\ContentType as eZContentType;
use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;

/**
 * Class Content.
 */
class Content
{
    /**
     * Repository eZ.
     *
     * @var Repository
     */
    protected $eZPublishRepository;

    /**
     * Constructor.
     */
    public function __construct(Repository $api)
    {
        $this->eZPublishRepository = $api;
    }

    /**
     * Get eZ Repository.
     *
     * @return Repository
     */
    public function getRepository()
    {
        return $this->eZPublishRepository;
    }

    /**
     * Easy access to the ContentService.
     *
     * @return ContentService
     */
    public function getContentService()
    {
        return $this->eZPublishRepository->getContentService();
    }

    /**
     * Easy access to the ContentTypeService.
     *
     * @return ContentTypeService
     */
    public function getContentTypeService()
    {
        return $this->eZPublishRepository->getContentTypeService();
    }

    /**
     * Easy access to the LocationService.
     *
     * @return LocationService
     */
    public function getLocationService()
    {
        return $this->eZPublishRepository->getLocationService();
    }

    /**
     * Change the user of the repository
     * Note: you need to keep the current user if you want to go back on the current user.
     */
    public function sudoRoot()
    {
        $this->eZPublishRepository->setCurrentUser($this->eZPublishRepository->getUserService()->loadUser(14));
    }

    /**
     * Create Content Wrapper.
     *
     * @param string $contentTypeIdentifier
     * @param int    $parentLocationId
     * @param array  $data
     * @param array  $options
     * @param string $lang
     *
     * @return ValueContent
     */
    public function createContent($contentTypeIdentifier, $parentLocationId, $data, $options = [], $lang = 'eng-US')
    {
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

    /**
     * Update Content Wrapper.
     *
     * @param array  $data
     * @param array  $options
     * @param string $lang
     *
     * @return ValueContent
     */
    public function updateContent(ValueContent $content, $data, $options = [], $lang = 'eng-US')
    {
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

    /**
     * Publish a version wrapper.
     *
     * @param array $options
     *
     * @return ValueContent
     */
    protected function publishVersion(ValueContent $draft, $options = [])
    {
        if (
            (array_key_exists('callback_before_publish', $options)) &&
            (is_callable($options['callback_before_publish']))
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
                    $content->contentInfo->publishedDate->getTimestamp() !=
                    $metadataUpdate->publishedDate->getTimestamp()
                ) {
                    $doUpdate = true;
                }
            }

            if (!empty($options['modified'])) {
                $metadataUpdate->modificationDate = $options['modified'];
                if (
                    $content->contentInfo->modificationDate->getTimestamp() !=
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

    /**
     * Autofill the Struct with the available field in $data.
     *
     * @param array $data
     */
    protected function autoFillStruct(eZContentType $contentType, ValueObject $contentStruct, $data)
    {
        /* @var ContentUpdateStruct|ContentUpdateStruct $contentStruct */

        foreach ($contentType->getFieldDefinitions() as $field) {
            /** @var FieldDefinition $field */
            $fieldName = $field->identifier;
            if (!array_key_exists($fieldName, $data)) {
                continue;
            }
            $fieldValue = $data[$fieldName];
            $contentStruct->setField($fieldName, $fieldValue);
        }
    }

    /**
     * Create/Update Sugar for trying to update else to create.
     *
     * @param string $contentTypeIdentifier
     * @param int    $parentLocationId
     * @param array  $data
     * @param string $remoteId
     * @param array  $options
     * @param string $lang
     *
     * @return ValueContent
     */
    public function createUpdateContent(
        $contentTypeIdentifier,
        $parentLocationId,
        $data,
        $remoteId,
        $options = [],
        $lang = 'eng-US'
    ) {
        $options['remoteId'] = $remoteId;
        try {
            $content = $this->getContentService()->loadContentByRemoteId($remoteId);
            if ((array_key_exists('do_no_update', $options)) && (true == $options['do_no_update'])) {
                return $content;
            }
            $newContent = $this->updateContent($content, $data, $options, $lang);
            if ((array_key_exists('callback_update', $options)) && (is_callable($options['callback_update']))) {
                $options['callback_update']($newContent);
            }
        } catch (NotFoundException $e) {
            $newContent = $this->createContent($contentTypeIdentifier, $parentLocationId, $data, $options, $lang);
            if ((array_key_exists('callback_create', $options)) && (is_callable($options['callback_create']))) {
                $options['callback_create']($newContent);
            }
        }

        return $newContent;
    }

    /**
     * Get + Create (no update) Sugar for trying to update else to create.
     *
     * @param string $contentTypeIdentifier
     * @param int    $parentLocationId
     * @param array  $data
     * @param string $remoteId
     * @param array  $options
     * @param string $lang
     *
     * @return ValueContent
     */
    public function getCreateContent(
        $contentTypeIdentifier,
        $parentLocationId,
        $data,
        $remoteId,
        $options = [],
        $lang = 'eng-US'
    ) {
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

    /**
     * Add Location(s) to the content.
     *
     * @param array $destinationLocationIds
     */
    public function addLocation(ValueContent $content, $destinationLocationIds = [])
    {
        $existingLocations = $this->getLocationService()->loadLocations($content->contentInfo);
        $existingLocationsIds = [];
        foreach ($existingLocations as $existingLocation) {
            $existingLocationsIds[] = $existingLocation->parentLocationId;
        }

        foreach ($destinationLocationIds as $destinationLocationId) {
            if (!in_array($destinationLocationId, $existingLocationsIds)) {
                $locationCreateStruct = $this->getLocationService()->newLocationCreateStruct($destinationLocationId);
                $this->getLocationService()->createLocation($content->contentInfo, $locationCreateStruct);
            }
        }
    }
}
