<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\Iterator;

class CallbackIteratorItemTransformer implements IteratorItemTransformerInterface
{
    /** @var callable(mixed): mixed */
    protected $callback;

    public function __construct(callable|array $callback)
    {
        $this->callback = $callback;
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke($item)
    {
        return call_user_func($this->callback, $item);
    }
}
