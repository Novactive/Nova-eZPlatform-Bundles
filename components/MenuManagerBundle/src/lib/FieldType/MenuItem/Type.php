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

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;
use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\Core\FieldType\Value as BaseValue;
use eZ\Publish\SPI\FieldType\Value as SPIValue;
use eZ\Publish\SPI\Persistence\Content\FieldValue as PersistenceValue;

class Type extends FieldType
{
    protected $validatorConfigurationSchema = [];

    /** @var ValueConverter */
    protected $valueConverter;

    /**
     * Type constructor.
     *
     * @param ValueConverter $valueConverter
     */
    public function __construct(ValueConverter $valueConverter)
    {
        $this->valueConverter = $valueConverter;
    }

    /**
     * Validates the validatorConfiguration of a FieldDefinitionCreateStruct or FieldDefinitionUpdateStruct.
     *
     * @param mixed $validatorConfiguration
     *
     * @return \eZ\Publish\SPI\FieldType\ValidationError[]
     */
    public function validateValidatorConfiguration($validatorConfiguration)
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
     *
     * @param FieldDefinition $fieldDefinition The field definition of the field
     * @param Value           $fieldValue      The field value for which an action is performed
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     *
     * @return \eZ\Publish\SPI\FieldType\ValidationError[]
     */
    public function validate(FieldDefinition $fieldDefinition, SPIValue $fieldValue)
    {
        $validationErrors = [];

        if ($this->isEmptyValue($fieldValue)) {
            return $validationErrors;
        }

        return $validationErrors;
    }

    /**
     * Returns the field type identifier for this field type.
     *
     * @return string
     */
    public function getFieldTypeIdentifier()
    {
        return 'menuitem';
    }

    /**
     * Returns the name of the given field value.
     *
     * It will be used to generate content name and url alias if current field is designated
     * to be used in the content name/urlAlias pattern.
     *
     * @param Value $value
     *
     * @return string
     */
    public function getName(SPIValue $value)
    {
        return (string) $value->menuItems;
    }

    public function getEmptyValue()
    {
        return new Value();
    }

    /**
     * Returns if the given $value is considered empty by the field type.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function isEmptyValue(SPIValue $value)
    {
        return null === $value->menuItems || empty($value->menuItems);
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
     *
     * @param Value $value
     *
     * @throws InvalidArgumentException if the value does not match the expected structure
     */
    protected function checkValueStructure(BaseValue $value)
    {
        if (!is_array($value->menuItems)) {
            throw new InvalidArgumentType(
                '$value->menuItems',
                'array',
                $value->menuItems
            );
        }
    }

    /**
     * Returns information for FieldValue->$sortKey relevant to the field type.
     *
     * @param Value $value
     *
     * @return array
     */
    protected function getSortInfo(BaseValue $value)
    {
        return null;
    }

    /**
     * Converts a persistence $fieldValue to a Value.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\FieldValue $fieldValue
     *
     * @return \eZ\Publish\Core\FieldType\Value
     */
    public function fromPersistenceValue(PersistenceValue $fieldValue)
    {
        return $this->fromHash(
            is_array($fieldValue->externalData) && !empty($fieldValue->externalData) ?
                $fieldValue->externalData :
                $fieldValue->data
        );
    }

    /**
     * Converts an $hash to the Value defined by the field type.
     *
     * @param mixed $hash
     *
     * @return Value $value
     */
    public function fromHash($hash)
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
     *
     * @return mixed
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
    public function isSearchable()
    {
        return true;
    }
}
