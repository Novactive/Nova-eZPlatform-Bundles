<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Accessor;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ItemAccessorInterface;
use ArrayAccess;
use Exception;

class ArrayAccessor extends AbstractItemAccessor implements ItemAccessorInterface, ArrayAccess
{
    /** @var array<string|int, mixed> */
    protected array $array;

    /**
     * @param array<string|int, mixed> $array
     */
    public function __construct(array $array)
    {
        $this->array = $array;
    }

    /**
     * @param int|string $offset
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->array);
    }

    /**
     * @param $offset
     */
    public function offsetGet($offset)
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
     * @param int|string $offset
     */
    public function offsetSet($offset, $value): void
    {
        $this->array[$offset] = $value;
    }

    /**
     * @param int|string $offset
     */
    public function offsetUnset($offset): void
    {
        unset($this->array[$offset]);
    }
}
