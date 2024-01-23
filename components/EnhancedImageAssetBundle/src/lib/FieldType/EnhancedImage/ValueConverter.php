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

use DOMDocument;
use eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter\ImageConverter;

class ValueConverter extends ImageConverter
{
    /**
     * {@inheritDoc}
     */
    protected function fillXml($imageData, $pathInfo, $timestamp): string
    {
        $xml = parent::fillXml($imageData, $pathInfo, $timestamp);

        $focusPoint = $imageData['focusPoint'] ?? null;
        if (is_array($focusPoint)) {
            $dom = new DOMDocument();
            $dom->loadXML($xml);
            $ezimageTag = $dom->documentElement;
            $ezimageTag->setAttribute('focuspoint_x', (string) $focusPoint['posX']);
            $ezimageTag->setAttribute('focuspoint_y', (string) $focusPoint['posY']);
            $xml = $dom->saveXML();
        }

        return $xml;
    }

    /**
     * {@inheritDoc}
     */
    protected function parseLegacyXml($xml): array
    {
        $extractedData = parent::parseLegacyXml($xml);

        $dom = new DOMDocument();
        $dom->loadXml($xml);

        $ezimageTag = $dom->documentElement;
        if ($ezimageTag->hasAttribute('focuspoint_x') && $ezimageTag->hasAttribute('focuspoint_y')) {
            $extractedData['focusPoint'] = [
                'posX' => (float) $ezimageTag->getAttribute('focuspoint_x'),
                'posY' => (float) $ezimageTag->getAttribute('focuspoint_y'),
            ];
        } else {
            $extractedData['focusPoint'] = [
                'posX' => 0,
                'posY' => 0,
            ];
        }

        return $extractedData;
    }
}
