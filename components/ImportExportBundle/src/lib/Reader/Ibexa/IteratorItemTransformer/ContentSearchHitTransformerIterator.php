<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\Ibexa\IteratorItemTransformer;

use AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\ObjectAccessorBuilder;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Iterator\IteratorItemTransformerInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;

class ContentSearchHitTransformerIterator implements IteratorItemTransformerInterface
{
    protected ObjectAccessorBuilder $valueAccessorBuilder;
    protected array $map;

    public function __construct(ObjectAccessorBuilder $valueAccessorBuilder)
    {
        $this->valueAccessorBuilder = $valueAccessorBuilder;
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke($item)
    {
        if ($item instanceof SearchHit && $item->valueObject instanceof Content) {
            return $this->valueAccessorBuilder->buildFromContent($item->valueObject);
        }

        return null;
    }
}
