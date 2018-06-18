<?php
/**
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 */

namespace Novactive\EzMenuManager\FieldType\MenuItem;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;
use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\Core\FieldType\Value as BaseValue;
use eZ\Publish\SPI\FieldType\Value as SPIValue;

class Type extends FieldType
{
    protected $validatorConfigurationSchema = [];

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

        //        foreach ($validatorConfiguration as $validatorIdentifier => $constraints) {
        //            if ('StringLengthValidator' !== $validatorIdentifier) {
        //                $validationErrors[] = new ValidationError(
        //                    "Validator '%validator%' is unknown",
        //                    null,
        //                    [
        //                        '%validator%' => $validatorIdentifier,
        //                    ]
        //                );
        //                continue;
        //            }
        //        }

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
     * @param \eZ\Publish\Core\FieldType\TextLine\Value $value
     *
     * @return string
     */
    public function getName(SPIValue $value)
    {
        return (string) $value->text;
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
        return null === $value->text || '' === trim($value->text);
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
        if (!is_string($value->text)) {
            throw new InvalidArgumentType(
                '$value->text',
                'string',
                $value->text
            );
        }
    }

    /**
     * Returns information for FieldValue->$sortKey relevant to the field type.
     *
     * @param \eZ\Publish\Core\FieldType\TextLine\Value $value
     *
     * @return array
     */
    protected function getSortInfo(BaseValue $value)
    {
        return $this->transformationProcessor->transformByGroup((string) $value, 'lowercase');
    }

    /**
     * Converts an $hash to the Value defined by the field type.
     *
     * @param mixed $hash
     *
     * @return \eZ\Publish\Core\FieldType\TextLine\Value $value
     */
    public function fromHash($hash)
    {
        if (null === $hash) {
            return $this->getEmptyValue();
        }

        return new Value($hash);
    }

    /**
     * Converts a $Value to a hash.
     *
     * @param \eZ\Publish\Core\FieldType\TextLine\Value $value
     *
     * @return mixed
     */
    public function toHash(SPIValue $value)
    {
        if ($this->isEmptyValue($value)) {
            return null;
        }

        return $value->text;
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
