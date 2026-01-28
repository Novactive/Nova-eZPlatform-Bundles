<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\Content\Field\ValueTransformer;

use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;

interface FieldValueTransformerInterface
{
    /**
     * @return mixed
     */
    public function __invoke(Field $field, FieldDefinition $fieldDefinition);
}
