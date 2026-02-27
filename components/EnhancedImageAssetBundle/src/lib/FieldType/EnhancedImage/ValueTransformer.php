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

use Ibexa\Contracts\Core\Repository\FieldType;
use Ibexa\Core\FieldType\Value as BaseValue;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ValueTransformer implements DataTransformerInterface
{
    public function __construct(
        private FieldType $fieldType,
        private BaseValue $initialValue,
        private string $valueClass
    ) {
    }

    public function transform(mixed $value): array
    {
        if (null === $value) {
            $value = $this->fieldType->getEmptyValue();
        }

        return array_merge(
            ['file' => null, 'remove' => false],
            [
                'alternativeText' => $value->alternativeText,
                'focusPoint' => $value->focusPoint,
            ]
        );
    }

    /**
     * @throws TransformationFailedException
     */
    public function reverseTransform(mixed $value): Value
    {
        /** @var Value $valueObject */
        $valueObject = $this->getReverseTransformedValue($value);

        if ($this->fieldType->isEmptyValue($valueObject)) {
            return $valueObject;
        }

        $valueObject->alternativeText = $value['alternativeText'];
        $valueObject->focusPoint = $value['focusPoint'];
        if ($value['isNewFocusPoint']) {
            $valueObject->isNewFocusPoint = true;
        }

        return $valueObject;
    }

    private function getReverseTransformedValue(array $value): BaseValue
    {
        if ($value['remove']) {
            return $this->fieldType->getEmptyValue();
        }

        if (null === $value['file']) {
            return clone $this->initialValue;
        }

        $properties = [
            'inputUri' => $value['file']->getRealPath(),
            'fileName' => $value['file']->getClientOriginalName(),
            'fileSize' => $value['file']->getSize(),
        ];

        return new $this->valueClass($properties);
    }
}
