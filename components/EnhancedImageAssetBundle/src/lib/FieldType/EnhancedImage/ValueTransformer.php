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

use Ibexa\AdminUi\Form\DataTransformer\FieldType\AbstractBinaryBaseTransformer;
use Symfony\Component\Form\DataTransformerInterface;

class ValueTransformer extends AbstractBinaryBaseTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): array
    {
        if (null === $value) {
            $value = $this->fieldType->getEmptyValue();
        }

        return array_merge(
            $this->getDefaultProperties(),
            [
                'alternativeText' => $value->alternativeText,
                'focusPoint' => $value->focusPoint,
            ]
        );
    }

    /**
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function reverseTransform(mixed $value): Value
    {
        /** @var Value $valueObject */
        $valueObject = parent::getReverseTransformedValue($value);

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
}
