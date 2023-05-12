<?php

/**
 * NovaeZEnhancedImageAssetBundle.
 *
 * @package   NovaeZEnhancedImageAssetBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZEnhancedImageAssetBundle/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Novactive\EzEnhancedImageAsset\FieldType\EnhancedImage;

use Ibexa\Contracts\Core\FieldType\ValidationError;
use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\Image\Type as ImageType;
use Ibexa\Core\FieldType\Value as BaseValue;

/**
 * The Image field type.
 */
class Type extends ImageType
{
    /**
     * Returns the field type identifier for this field type.
     */
    public function getFieldTypeIdentifier(): string
    {
        return 'enhancedimage';
    }

    /**
     * Returns the fallback default value of field type when no such default
     * value is provided in the field definition in content types.
     *
     *@throws InvalidArgumentType
     */
    public function getEmptyValue(): Value
    {
        return new Value();
    }

    /**
     * Inspects given $inputValue and potentially converts it into a dedicated value object.
     *
     * @param string|array|Value $inputValue
     *
     *@throws InvalidArgumentType
     */
    protected function createValueFromInput($inputValue): Value
    {
        if (is_string($inputValue)) {
            $inputValue = Value::fromString($inputValue);
        }

        if (is_array($inputValue)) {
            if (isset($inputValue['inputUri']) && file_exists($inputValue['inputUri'])) {
                $inputValue['fileSize'] = filesize($inputValue['inputUri']);
                if (!isset($inputValue['fileName'])) {
                    $inputValue['fileName'] = basename($inputValue['inputUri']);
                }
            }

            $inputValue = new Value($inputValue);
        }

        return $inputValue;
    }

    /**
     * Throws an exception if value structure is not of expected format.
     *
     * @throws InvalidArgumentException
     */
    protected function checkValueStructure(BaseValue $value): void
    {
        parent::checkValueStructure($value);
    }

    /**
     * Validates a field based on the validators in the field definition.
     *
     * @throws InvalidArgumentException
     *
     * @return ValidationError[]
     */
    public function validate(FieldDefinition $fieldDefinition, SPIValue $fieldValue): array
    {
        return parent::validate($fieldDefinition, $fieldValue);
    }

    /**
     * Converts an $hash to the Value defined by the field type.
     *
     * @param $hash
     *
     * @throws InvalidArgumentType
     *
     * @return Value $value
     */
    public function fromHash($hash): Value
    {
        if (null === $hash) {
            return $this->getEmptyValue();
        }
        if (isset($hash['focusPoint']) && !$hash['focusPoint'] instanceof FocusPoint) {
            $hash['focusPoint'] = new FocusPoint(
                (float) $hash['focusPoint']['posX'],
                (float) $hash['focusPoint']['posY']
            );
        }

        return new Value($hash);
    }

    /**
     * Converts a $Value to a hash.
     *
     * @return mixed|null
     */
    public function toHash(SPIValue $value)
    {
        if ($this->isEmptyValue($value)) {
            return null;
        }

        $hash = parent::toHash($value);
        if ($value->focusPoint instanceof FocusPoint) {
            $hash['focusPoint'] = [
                'posX' => $value->focusPoint->getPosX(),
                'posY' => $value->focusPoint->getPosY(),
            ];
        }
        $hash['isNewFocusPoint'] = $value->isNewFocusPoint;

        return $hash;
    }

    /**
     * Converts a persistence $fieldValue to a Value.
     *
     * @throws InvalidArgumentType
     */
    public function fromPersistenceValue(FieldValue $fieldValue): Value
    {
        if (null === $fieldValue->data) {
            return $this->getEmptyValue();
        }

        $baseValue = parent::fromPersistenceValue($fieldValue);
        $properties = [];
        foreach (get_object_vars($baseValue) as $property => $propertyValue) {
            if ($baseValue->__isset($property)) {
                $properties[$property] = $propertyValue;
            }
        }

        $properties['focusPoint'] = ($fieldValue->data['focusPoint'] ?? new FocusPoint());
        // Restored data comes in $data, since it has already been processed
        // there might be more data in the persistence value than needed here
        return $this->fromHash($properties);
    }
}
