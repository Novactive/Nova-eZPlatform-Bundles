<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Content;

use DateTime;
use Exception;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;

class IbexaContentCreator extends AbstractIbexaContentHandler
{
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
    public function __invoke(
        string $contentTypeIdentifier,
        array $parentLocationIdList,
        array $fieldsByLanguages,
        string $remoteId,
        int $ownerId = null,
        string $languageCode = 'eng-GB',
        int $sectionId = null,
        $modificationDate = null,
        bool $hidden = false
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
}
