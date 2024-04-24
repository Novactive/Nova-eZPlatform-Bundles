<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\Ibexa\ContentList;

use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderOptions;

/**
 * @property int $parentLocationId
 */
class IbexaContentListReaderOptions extends ReaderOptions
{
    protected int $parentLocationId;
}
