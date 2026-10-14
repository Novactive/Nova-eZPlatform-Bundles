<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\Ibexa\ContentList;

use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderOptions;

/**
 * @property int               $parentLocationId
 * @property array<int|string> $contentTypes
 */
class IbexaContentListReaderOptions extends ReaderOptions
{
    protected ?int $parentLocationId = null;
    /**
     * @var array<int|string>
     */
    protected array $contentTypes = [];
}
