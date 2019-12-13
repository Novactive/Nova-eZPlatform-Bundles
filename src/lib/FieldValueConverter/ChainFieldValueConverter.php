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

namespace Novactive\EzEnhancedImageAsset\FieldValueConverter;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\FieldType\ImageAsset\Value as ImageAssetValue;

class ChainFieldValueConverter
{
    /** @var FieldValueConverterInterface[] */
    protected $converters;

    /**
     * ChainFieldConverter constructor.
     *
     * @param FieldValueConverterInterface[] $converters
     */
    public function __construct(iterable $converters)
    {
        foreach ($converters as $converter) {
            $this->addConverter($converter);
        }
    }

    public function addConverter(FieldValueConverterInterface $converter): void
    {
        $this->converters[] = $converter;
    }

    public function toImageAssetValue(Content $content, Field $field): ?ImageAssetValue
    {
        foreach ($this->converters as $converter) {
            if (!$converter->support($field->fieldTypeIdentifier)) {
                continue;
            }

            return $converter->toImageAssetValue($content, $field);
        }

        return null;
    }
}
