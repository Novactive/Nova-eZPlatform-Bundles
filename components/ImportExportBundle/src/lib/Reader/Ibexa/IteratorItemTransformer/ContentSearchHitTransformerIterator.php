<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\Ibexa\IteratorItemTransformer;

use AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\ObjectAccessor;
use AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\ObjectAccessorBuilder;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Iterator\IteratorItemTransformerInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;

/**
 * @implements IteratorItemTransformerInterface<SearchHit, ObjectAccessor>
 */
class ContentSearchHitTransformerIterator implements IteratorItemTransformerInterface
{
    public function __construct(
        protected ObjectAccessorBuilder $valueAccessorBuilder
    ) {
    }

    public function __invoke(mixed $item): ?ObjectAccessor
    {
        if ($item->valueObject instanceof Content) {
            return $this->valueAccessorBuilder->buildFromContent($item->valueObject);
        }

        return null;
    }
}
