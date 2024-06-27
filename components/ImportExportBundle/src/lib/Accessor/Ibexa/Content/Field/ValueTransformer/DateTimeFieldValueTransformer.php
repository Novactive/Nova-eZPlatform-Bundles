<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\Content\Field\ValueTransformer;

use AlmaviaCX\Bundle\IbexaImportExport\Accessor\DatetimeAccessor;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;

class DateTimeFieldValueTransformer implements FieldValueTransformerInterface
{
    public function __invoke(Field $field, FieldDefinition $fieldDefinition): DatetimeAccessor
    {
        return new DatetimeAccessor($field->getValue()->value);
    }
}
