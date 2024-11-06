<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa;

use AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\Content\ContentAccessorBuilder;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;

class ObjectAccessorBuilder
{
    protected LocationService $locationService;
    protected ContentService $contentService;
    protected ContentAccessorBuilder $contentAccessorBuilder;

    public function __construct(
        LocationService $locationService,
        ContentService $contentService,
        ContentAccessorBuilder $contentAccessorBuilder
    ) {
        $this->locationService = $locationService;
        $this->contentService = $contentService;
        $this->contentAccessorBuilder = $contentAccessorBuilder;
    }

    public function buildFromContent(Content $content): ObjectAccessor
    {
        $initializers = [
            'content' => function (ObjectAccessor $instance) use ($content) {
                return $this->contentAccessorBuilder->buildFromContent($content);
            },
            'mainLocation' => function (ObjectAccessor $instance) use ($content) {
                return $content->contentInfo->getMainLocation();
            },
            'contentType' => function (ObjectAccessor $instance) use ($content) {
                return $content->contentInfo->getContentType();
            },
            'locations' => function (ObjectAccessor $instance) use ($content) {
                return $this->locationService->loadLocations($content->contentInfo);
            },
        ];

        return $this->createLazyGhost($initializers);
    }

    protected function createLazyGhost(array $initializers): ObjectAccessor
    {
        return ObjectAccessor::createLazyGhost($initializers);
    }
}
