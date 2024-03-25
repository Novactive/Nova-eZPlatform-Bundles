<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Search\Common\FieldValueMapper;

use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Core\Search\Common\FieldValueMapper;

class MultipleDateRangeMapper extends FieldValueMapper
{
    use DateMapperTrait;

    public function canMap(Field $field): bool
    {
        return $field->getType() instanceof \Novactive\EzSolrSearchExtra\Search\FieldType\MultipleDateField;
    }

    public function map(Field $field)
    {
        $values = [];

        foreach ((array) $field->getValue() as $value) {
            $values[] = $this->mapDateRange(...$value);
        }

        return $values;
    }
}
