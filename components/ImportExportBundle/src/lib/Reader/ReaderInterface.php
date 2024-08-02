<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader;

use AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentInterface;

interface ReaderInterface extends ComponentInterface
{
    /**
     * @return ReaderIteratorInterface<\AlmaviaCX\Bundle\IbexaImportExport\Item\ItemAccessorInterface>
     */
    public function __invoke(): ReaderIteratorInterface;
}
