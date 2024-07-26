<?php

namespace AlmaviaCX\Bundle\IbexaRichTextExtra\FieldType\BinaryFile;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\FieldType\BinaryFile\Value as BinaryFileValue;

class Mapper
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    /** @var int */
    private $contentTypeId = null;

    public function __construct(
        ContentService $contentService,
        LocationService $locationService,
        ContentTypeService $contentTypeService,
        ConfigResolverInterface $configResolver
    ) {
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->contentTypeService = $contentTypeService;
        $this->configResolver = $configResolver;
    }

    /**
     * Creates an Image Asset.
     */
    public function createAsset(string $name, BinaryFileValue $binaryFile, string $languageCode): Content
    {
        $mappings = $this->getMappings();

        $contentType = $this->contentTypeService->loadContentTypeByIdentifier(
            $mappings['content_type_identifier']
        );

        $contentCreateStruct = $this->contentService->newContentCreateStruct($contentType, $languageCode);
        $contentCreateStruct->setField($mappings['name_field_identifier'], $name);
        $contentCreateStruct->setField($mappings['content_field_identifier'], $binaryFile);

        $contentDraft = $this->contentService->createContent($contentCreateStruct, [
            $this->locationService->newLocationCreateStruct($mappings['parent_location_id']),
        ]);

        return $this->contentService->publishVersion($contentDraft->versionInfo);
    }

    /**
     * Returns field value of the Image Asset from specified content.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     *
     * @return \Ibexa\Core\FieldType\Image\Value
     */
    public function getAssetValue(Content $content): BinaryFileValue
    {
        if (!$this->isBinaryFile($content)) {
            throw new InvalidArgumentException('contentId', "Content {$content->id} is not an image asset.");
        }

        return $content->getFieldValue($this->getContentFieldIdentifier());
    }

    /**
     * Returns TRUE if content is an Image Asset.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function isBinaryFile(Content $content): bool
    {
        if (null === $this->contentTypeId) {
            $contentType = $this->contentTypeService->loadContentTypeByIdentifier(
                $this->getContentTypeIdentifier()
            );

            $this->contentTypeId = $contentType->id;
        }

        return $content->contentInfo->contentTypeId === $this->contentTypeId;
    }

    /**
     * Return identifier of the Content Type used as Assets.
     */
    public function getContentTypeIdentifier(): string
    {
        return $this->getMappings()['content_type_identifier'];
    }

    /**
     * Return identifier of the field used to store Image Asset value.
     */
    public function getContentFieldIdentifier(): string
    {
        return $this->getMappings()['content_field_identifier'];
    }

    /**
     * Return ID of the base location for the Image Assets.
     */
    public function getParentLocationId(): int
    {
        return $this->getMappings()['parent_location_id'];
    }

    public function getMappings(): array
    {
        return $this->configResolver->getParameter('fieldtypes.binaryfile.mappings');
    }
}
