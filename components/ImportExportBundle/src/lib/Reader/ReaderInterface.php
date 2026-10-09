<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader;

use AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Item\ItemAccessorInterface;

/**
 * @template TReaderOptions of ReaderOptions
 * @extends  ComponentInterface<TReaderOptions>
 */
interface ReaderInterface extends ComponentInterface
{
    /**
     * @return ReaderIteratorInterface<mixed, ItemAccessorInterface>
     */
    public function __invoke();

    public static function getDetailsTemplate(): ?string;
}
