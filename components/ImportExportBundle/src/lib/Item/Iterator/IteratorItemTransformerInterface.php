<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\Iterator;

interface IteratorItemTransformerInterface
{
    /**
     * @param $item
     */
    public function __invoke($item);
}
