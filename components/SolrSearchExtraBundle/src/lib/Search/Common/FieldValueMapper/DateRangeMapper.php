<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Search\Common\FieldValueMapper;

use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Core\Search\Common\FieldValueMapper;

class DateRangeMapper extends FieldValueMapper
{
    use DateMapperTrait;

    public function canMap(Field $field): bool
    {
        return $field->getType() instanceof \Novactive\EzSolrSearchExtra\Search\FieldType\DateRangeField;
    }

    public function map(Field $field)
    {
        return $this->mapDateRange(...$field->getValue());
    }
}
