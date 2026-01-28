<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Accessor;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ItemAccessorInterface;
use ArrayAccess;
use Exception;

/**
 * @template TKey
 * @template TValue
 * @implements ArrayAccess<TKey, TValue>
 */
class ArrayAccessor extends AbstractItemAccessor implements ItemAccessorInterface, ArrayAccess
{
    /**
     * @param array<TKey, TValue> $array
     */
    public function __construct(protected array $array)
    {
    }

    /**
     * @param TKey $offset
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->array);
    }

    /**
     * @param TKey $offset
     *
     * @throws \Exception
     *
     * @return TValue
     */
    public function offsetGet(mixed $offset): mixed
    {
        if (!$this->offsetExists($offset)) {
            throw new Exception(
                sprintf(
                    'Undefined offset: %s. 
Available offsets are %s',
                    $offset,
                    implode(' / ', array_map(function ($value) {
                        return "'$value'";
                    }, array_keys($this->array)))
                )
            );
        }

        return $this->array[$offset];
    }

    /**
     * @param TKey   $offset
     * @param TValue $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->array[$offset] = $value;
    }

    /**
     * @param TKey $offset
     */
    public function offsetUnset($offset): void
    {
        unset($this->array[$offset]);
    }
}
