<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader;

use Countable;
use Iterator;

/**
 * @phpstan-type ProcessableItem mixed
 * @template TKey
 * @template-covariant TValue
 * @extends Iterator<TKey, TValue>
 */
interface ReaderIteratorInterface extends Iterator, Countable
{
}
