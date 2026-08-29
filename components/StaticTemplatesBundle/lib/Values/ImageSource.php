<?php

/**
 * @copyright Novactive
 * Date: 18/07/2022
 */

declare(strict_types=1);

namespace Novactive\StaticTemplates\Values;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Novactive\EzEnhancedImageAsset\FieldType\EnhancedImage\FocusPoint;

class ImageSource extends ValueObject
{
    public string $uri;

    public ?int $width = null;

    public ?int $height = null;

    public string $media;

    public ?FocusPoint $focusPoint = null;

    public ?string $mimeType = null;

    public ?string $variation = null;

    public function getTagAttributes(array $attrs = []): array
    {
        $this->initiateArrayAttribute($attrs, 'srcset');
        $this->initiateArrayAttribute($attrs, 'class');
        $attrs['class'][] = 'enhancedimage--img--lazyload';
        if ($this->focusPoint) {
            $attrs['data-focus-x'] = $this->focusPoint->getPosX();
            $attrs['data-focus-y'] = $this->focusPoint->getPosY();
            $attrs['class'][] = 'enhancedimage--focused-img';
        }
        $attrs['srcset'] = $this->uri;
        $attrs['data-width'] = $this->width;
        $attrs['data-height'] = $this->height;
        $attrs['data-variation'] = $this->variation;
        $attrs['media'] = $this->media;
        if ($this->mimeType) {
            $attrs['type'] = $this->mimeType;
        }
        $attrs['class'] = implode(' ', $attrs['class']);

        return $attrs;
    }

    protected function initiateArrayAttribute(array &$attributes, string $attributeName): void
    {
        if (!isset($attributes[$attributeName])) {
            $attributes[$attributeName] = [];
        } else {
            $attributes[$attributeName] = !is_array($attributes[$attributeName]) ?
                [$attributes[$attributeName]] :
                $attributes[$attributeName];
        }
    }
}
