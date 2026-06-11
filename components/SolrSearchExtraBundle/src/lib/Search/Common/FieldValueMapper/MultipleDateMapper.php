<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Search\Common\FieldValueMapper;

use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Core\Search\Common\FieldValueMapper;

class MultipleDateMapper extends FieldValueMapper
{
    use DateMapperTrait;

    public function canMap(Field $field): bool
    {
        return $field->getType() instanceof \Novactive\EzSolrSearchExtra\Search\FieldType\MultipleDateField;
    }

    /**
     * @return array<string>
     */
    public function map(Field $field): array
    {
        $values = [];

        foreach ((array) $field->getValue() as $value) {
            $values[] = $this->mapDate($value);
        }

        return $values;
    }
}
