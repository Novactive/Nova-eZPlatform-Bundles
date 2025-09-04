<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\Content;

use AlmaviaCX\Bundle\IbexaImportExport\Accessor\DatetimeAccessor;
use AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\Content\Field\ContentFieldAccessorBuilder;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;

class ContentAccessorBuilder
{
    public function __construct(
        protected ContentFieldAccessorBuilder $contentFieldAccessorBuilder,
        protected ContentService $contentService
    ) {
    }

    public function buildFromContent(Content $content): ContentAccessor
    {
        return $this->create(function () use ($content) {
            return $content;
        });
    }

    public function create(callable $contentInitializer): ContentAccessor
    {
        $initializers = [
            "\0*\0content" => $contentInitializer,
            'id' => function (ContentAccessor $instance) {
                return $instance->getContent()->id;
            },
            'mainLocationId' => function (ContentAccessor $instance) {
                return $instance->getContent()->contentInfo->mainLocationId;
            },
            'fields' => function (ContentAccessor $instance) {
                $content = $instance->getContent();
                $fields = [];
                foreach ($content->getFields() as $field) {
                    $fieldDefinition = $content->getContentType()->getFieldDefinition($field->fieldDefIdentifier);
                    $fields[$field->fieldDefIdentifier] = $this->contentFieldAccessorBuilder->build(
                        $field,
                        $fieldDefinition
                    );
                }

                return $fields;
            },
            'names' => function (ContentAccessor $instance) {
                return $instance->getContent()->getVersionInfo()->getNames();
            },
            'creationDate' => function (ContentAccessor $instance) {
                return new DatetimeAccessor($instance->getContent()->versionInfo->creationDate);
            },
        ];

        return ContentAccessor::createLazyGhost($initializers);
    }

    public function buildFromContentId(int $contentId): ContentAccessor
    {
        return $this->create(function () use ($contentId) {
            return $this->contentService->loadContent($contentId);
        });
    }
}
