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

use Ibexa\AdminUi\Form\DataTransformer\FieldType\ImageValueTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ValueTransformer extends ImageValueTransformer
{
    /**
     * @param Value $value
     */
    public function transform($value): array
    {
        if (null === $value) {
            $value = $this->fieldType->getEmptyValue();
        }

        $properties = parent::transform($value);

        return array_merge(
            $properties,
            ['focusPoint' => $value->focusPoint]
        );
    }

    /**
     * @param array $value
     *
     * @throws TransformationFailedException
     */
    public function reverseTransform($value): Value
    {
        /** @var Value $valueObject */
        $valueObject = parent::reverseTransform($value);

        if ($this->fieldType->isEmptyValue($valueObject)) {
            return $valueObject;
        }

        $valueObject->focusPoint = $value['focusPoint'];
        if ($value['isNewFocusPoint']) {
            $valueObject->isNewFocusPoint = true;
        }

        return $valueObject;
    }
}
