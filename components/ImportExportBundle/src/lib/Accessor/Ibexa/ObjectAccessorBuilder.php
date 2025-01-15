<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa;

use AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\Content\ContentAccessor;
use AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\Content\ContentAccessorBuilder;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;

class ObjectAccessorBuilder
{
    public function __construct(
        protected LocationService $locationService,
        protected ContentService $contentService,
        protected ContentAccessorBuilder $contentAccessorBuilder
    ) {
    }

    public function buildFromContent(Content $content): ObjectAccessor
    {
        $initializers = [
            'content' => function (ObjectAccessor $instance) use ($content): ContentAccessor {
                return $this->contentAccessorBuilder->buildFromContent($content);
            },
            'mainLocation' => function (ObjectAccessor $instance) use ($content): Location {
                return $content->contentInfo->getMainLocation();
            },
            'contentType' => function (ObjectAccessor $instance) use ($content): ContentType {
                return $content->contentInfo->getContentType();
            },
            'locations' => function (ObjectAccessor $instance) use ($content): array {
                return $this->locationService->loadLocations($content->contentInfo);
            },
        ];

        return $this->createLazyGhost($initializers);
    }

    /**
     * @param array<string, callable(ObjectAccessor):mixed> $initializers
     *
     * @return \AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\ObjectAccessor
     */
    protected function createLazyGhost(array $initializers): ObjectAccessor
    {
        return ObjectAccessor::createLazyGhost($initializers);
    }
}
