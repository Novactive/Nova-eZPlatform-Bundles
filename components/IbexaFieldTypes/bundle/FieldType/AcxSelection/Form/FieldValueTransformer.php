<?php

declare(strict_types=1);

namespace AlmaviaCX\Ibexa\Bundle\FieldTypes\FieldType\AcxSelection\Form;

use AlmaviaCX\Ibexa\Bundle\FieldTypes\FieldType\AcxSelection\Value;
use Symfony\Component\Form\DataTransformerInterface;

final class FieldValueTransformer implements DataTransformerInterface
{
    public function __construct(protected bool $isMultiple = false)
    {
    }
    public function transform($value)
    {
        if (!$value instanceof Value) {
            return null;
        }
        $selection = (array) ($value->selection ?? []);

        return $this->isMultiple === true ? $selection : ($selection[0] ?? null);
    }

    public function reverseTransform($value): ?Value
    {
        $value = (array) $value;
        return new Value($this->isMultiple === false ? [reset($value)]: $value);
    }
}
