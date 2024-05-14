<?php

namespace AlmaviaCX\Ibexa\Bundle\FieldTypes\FieldType\AcxSelection;

use Ibexa\Contracts\Core\FieldType\Value as ValueInterface;

class Value implements ValueInterface
{
    public array $selection = [];

    public function __construct(array $selection = [])
    {
        $this->selection = $selection;
    }

    public function getSelection(): string
    {
        return $this->selection[0] ?? '';
    }

    public function __toString()
    {
        return implode(',', $this->selection);
    }
}
