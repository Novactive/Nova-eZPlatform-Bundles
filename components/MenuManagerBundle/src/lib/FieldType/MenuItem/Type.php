<?php

/**
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 */

namespace Novactive\EzMenuManager\FieldType\MenuItem;

use Ibexa\Contracts\Core\FieldType\ValidationError;
use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\Core\FieldType\Value as ValueInterface;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue as PersistenceValue;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\FieldType;
use Ibexa\Core\FieldType\Value as BaseValue;
use Novactive\EzMenuManager\FieldType\MenuItem\value as Value;

class Type extends FieldType
{
    protected $validatorConfigurationSchema = [];

    protected ValueConverter $valueConverter;

    /**
     * Type constructor.
     */
    public function __construct(ValueConverter $valueConverter)
    {
        $this->valueConverter = $valueConverter;
    }

    /**
     * Validates the validatorConfiguration of a FieldDefinitionCreateStruct or FieldDefinitionUpdateStruct.
     *
     * @return ValidationError[]
     */
    public function validateValidatorConfiguration($validatorConfiguration): array
    {
        $validationErrors = [];

        if (!$validatorConfiguration) {
            return $validationErrors;
        }

        return $validationErrors;
    }

    /**
     * Validates a field based on the validators in the field definition.
     *
     * @param FieldDefinition $fieldDefinition The field definition of the field
     * @param SPIValue $value The field value for which an action is performed
     *
     * @return \eZ\Publish\SPI\FieldType\ValidationError[]
     */
    public function validate(FieldDefinition $fieldDefinition, SPIValue $value): array
    {
        $validationErrors = [];

        if ($this->isEmptyValue($value)) {
            return $validationErrors;
        }

        return $validationErrors;
    }

    /**
     * Returns the field type identifier for this field type.
     *
     * @return string
     */
    public function getFieldTypeIdentifier(): string
    {
        return 'menuitem';
    }

    /**
     * Returns the name of the given field value.
     *
     * It will be used to generate content name and url alias if current field is designated
     * to be used in the content name/urlAlias pattern.
     */
    public function getName(ValueInterface $value, FieldDefinition $fieldDefinition, string $languageCode): string
    {
        if ($value instanceof Value) {
            return (string) $value->menuItems;
        }
        return '';
    }

    public function getEmptyValue(): Value
    {
        return new Value();
    }

    /**
     * Returns if the given $value is considered empty by the field type.
     *
     * @param SPIValue $value
     *
     * @return bool
     */
    public function isEmptyValue(SPIValue $value): bool
    {
        return empty($value->menuItems);
    }

    protected function createValueFromInput($inputValue)
    {
        if (is_string($inputValue)) {
            $inputValue = new Value($inputValue);
        }

        return $inputValue;
    }

    /**
     * Throws an exception if value structure is not of expected format.
     *
     * @param Value $value
     *
     * @throws InvalidArgumentType if the value does not match the expected structure
     */
    protected function checkValueStructure(BaseValue $value): void
    {
        if (!is_array($value->menuItems)) {
            throw new InvalidArgumentType('$value->menuItems', 'array', $value->menuItems);
        }
    }

    /**
     * Returns information for FieldValue->$sortKey relevant to the field type.
     *
     * @param Value $value
     *
     * @return array|null
     */
    protected function getSortInfo(BaseValue $value): ?array
    {
        return null;
    }

    /**
     * Converts a persistence $fieldValue to a Value.
     *
     * @param PersistenceValue $fieldValue
     * @return value
     */
    public function fromPersistenceValue(PersistenceValue $fieldValue): Value
    {
        return $this->fromHash(
            is_array($fieldValue->externalData) && !empty($fieldValue->externalData) ?
                $fieldValue->externalData :
                $fieldValue->data
        );
    }

    /**
     * Converts a $hash to the Value defined by the field type.
     *
     * @return Value $value
     */
    public function fromHash($hash): Value
    {
        if (null === $hash) {
            return $this->getEmptyValue();
        }

        return $this->valueConverter->fromHash($hash);
    }

    /**
     * Converts a $Value to a hash.
     *
     * @param Value $value
     */
    public function toHash(SPIValue $value)
    {
        if ($this->isEmptyValue($value)) {
            return null;
        }

        return $this->valueConverter->toHash($value);
    }

    /**
     * Returns whether the field type is searchable.
     *
     * @return bool
     */
    public function isSearchable(): bool
    {
        return true;
    }
}
