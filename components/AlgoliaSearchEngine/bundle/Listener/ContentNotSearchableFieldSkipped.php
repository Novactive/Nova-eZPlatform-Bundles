<?php

/**
 * Nova eZ Algolia Search Engine.
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @licence   "SEE FULL LICENSE OPTIONS IN LICENSE.md"
 *            Nova eZ Algolia Search Engine is tri-licensed, meaning you must choose one of three licenses to use:
 *                - Commercial License: a paid license, meant for commercial use. The default option for most users.
 *                - Creative Commons Non-Commercial No-Derivatives: meant for trial and non-commercial use.
 *                - GPLv3 License: meant for open-source projects.
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZAlgoliaSearchEngine\Listener;

use eZ\Bundle\EzPublishCoreBundle\Imagine\ImageAsset\AliasGenerator;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Field as ContentField;
use eZ\Publish\SPI\Search\Field;
use eZ\Publish\SPI\Search\FieldType\StringField;
use Novactive\Bundle\eZAlgoliaSearchEngine\Event\ContentTranslationDataFieldConvertEvent;

class ContentNotSearchableFieldSkipped
{
    /**
     * @var AliasGenerator
     */
    private $imageVariationHandler;

    /**
     * @var ContentService
     */
    private $contentService;

    public function __construct(AliasGenerator $imageVariationHandler, ContentService $contentService)
    {
        $this->imageVariationHandler = $imageVariationHandler;
        $this->contentService = $contentService;
    }

    public function __invoke(ContentTranslationDataFieldConvertEvent $event): void
    {
        $field = $event->getField();
        $fieldDefinition = $event->getFieldDefinition();
        $document = $event->getDocument();
        $content = $event->getContent();

        if ($fieldDefinition->id !== $field->fieldDefinitionId) {
            return;
        }

        if ('ezimage' === $field->type && null !== $field->value->data) {
            $valueContent = $this->contentService->loadContent(
                $content->versionInfo->contentInfo->id,
                [$document->languageCode],
                $content->versionInfo->versionNo
            );

            /* @var ContentField $imageField */
            $imageField = $valueContent->getField($fieldDefinition->identifier);

            $variation = $this->imageVariationHandler->getVariation(
                $imageField,
                $valueContent->versionInfo,
                'medium'
            );

            $document->fields[] = new Field(
                "{$valueContent->getContentType()->identifier}_{$fieldDefinition->identifier}_uri",
                parse_url($variation->uri)['path'],
                new StringField()
            );
        }

        if ('ezimageasset' === $field->type && isset($field->value->data['destinationContentId'])) {
            $valueContent = $this->contentService->loadContent(
                $content->versionInfo->contentInfo->id,
                [$document->languageCode],
                $content->versionInfo->versionNo
            );

            $relationContent = $this->contentService->loadContent($field->value->data['destinationContentId']);

            /* @var ContentField $imageField */
            $imageField = $relationContent->getField('image');

            $variation = $this->imageVariationHandler->getVariation(
                $imageField,
                $relationContent->versionInfo,
                'medium'
            );

            $document->fields[] = new Field(
                "{$valueContent->getContentType()->identifier}_{$fieldDefinition->identifier}_uri",
                parse_url($variation->uri)['path'],
                new StringField()
            );
        }
    }
}
