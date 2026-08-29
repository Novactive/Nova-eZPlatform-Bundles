<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\Json;

use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderOptions;

/**
 * @property mixed  $json
 * @property string $type
 */
class JsonReaderOptions extends ReaderOptions
{
    protected mixed $json;

    public function getJson(): mixed
    {
        return $this->json;
    }
}
