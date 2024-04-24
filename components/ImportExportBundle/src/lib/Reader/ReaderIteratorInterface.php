<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader;

use Iterator;

interface ReaderIteratorInterface extends Iterator
{
    public function getTotalCount(): int;
}
