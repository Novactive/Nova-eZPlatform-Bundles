<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa;

use AlmaviaCX\Bundle\IbexaImportExport\Accessor\AbstractItemAccessor;
use AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\Content\ContentAccessor;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Symfony\Component\VarExporter\LazyGhostTrait;

class ObjectAccessor extends AbstractItemAccessor
{
    use LazyGhostTrait;

    public ContentAccessor $content;
    public Location $mainLocation;
    /**
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\Location[]
     */
    public array $locations = [];
    public ContentType $contentType;
}
