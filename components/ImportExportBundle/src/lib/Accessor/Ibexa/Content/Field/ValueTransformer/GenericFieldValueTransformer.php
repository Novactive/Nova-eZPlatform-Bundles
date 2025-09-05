<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\Content\Field\ValueTransformer;

use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Symfony\Component\PropertyAccess\PropertyAccess;

class GenericFieldValueTransformer implements FieldValueTransformerInterface
{
    protected string $propertyName = 'value';

    public function __construct(
        string $propertyName = 'value'
    ) {
        $this->propertyName = $propertyName;
    }

    public function __invoke(Field $field, FieldDefinition $fieldDefinition)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        return $accessor->getValue($field, $this->propertyName);
    }
}
