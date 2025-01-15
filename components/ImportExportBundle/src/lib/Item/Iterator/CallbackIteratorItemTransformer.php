<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\Iterator;

/**
 * @template TSourceItem
 * @template TResultItem
 * @implements IteratorItemTransformerInterface<TSourceItem, TResultItem>
 */
class CallbackIteratorItemTransformer implements IteratorItemTransformerInterface
{
    /**
     * @param callable(TSourceItem $item): TResultItem $callback
     */
    public function __construct(
        protected $callback
    ) {
    }

    public function __invoke(mixed $item): mixed
    {
        return call_user_func($this->callback, $item);
    }
}
