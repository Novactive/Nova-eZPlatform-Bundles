<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Search\Common\FieldValueMapper;

use DateTime;
use Exception;
use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Core\Search\Common\FieldValueMapper;
use InvalidArgumentException;

class MultipleDateMapper extends FieldValueMapper
{
    public function canMap(Field $field): bool
    {
        return $field->getType() instanceof \Novactive\EzSolrSearchExtra\Search\FieldType\MultipleDateField;
    }

    public function map(Field $field)
    {
        $values = [];

        foreach ((array) $field->getValue() as $value) {
            $values[] = $this->mapDate($value);
        }

        return $values;
    }

    protected function mapDate($value)
    {
        if (is_numeric($value)) {
            $date = new DateTime("@{$value}");
        } else {
            try {
                $date = new DateTime($value);
            } catch (Exception $e) {
                throw new InvalidArgumentException('Invalid date provided: '.$value);
            }
        }

        return $date->format('Y-m-d\\TH:i:s\\Z');
    }
}
