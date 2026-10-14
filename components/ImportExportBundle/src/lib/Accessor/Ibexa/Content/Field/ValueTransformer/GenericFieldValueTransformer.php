<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\Content\Field\ValueTransformer;

use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Symfony\Component\PropertyAccess\PropertyAccess;

class GenericFieldValueTransformer implements FieldValueTransformerInterface
{
    public function __construct(
        protected string $propertyName = 'value'
    ) {
    }

    public function __invoke(Field $field, FieldDefinition $fieldDefinition): mixed
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        return $accessor->getValue($field, $this->propertyName);
    }
}
