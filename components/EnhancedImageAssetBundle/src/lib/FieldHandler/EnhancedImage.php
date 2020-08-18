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

namespace Novactive\EzEnhancedImageAsset\FieldHandler;

use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;
use Kaliop\eZMigrationBundle\API\FieldValueConverterInterface;
use Kaliop\eZMigrationBundle\Core\FieldHandler\FileFieldHandler;
use Novactive\EzEnhancedImageAsset\FieldType\EnhancedImage\FocusPoint;
use Novactive\EzEnhancedImageAsset\FieldType\EnhancedImage\Value as EnhancedImageValue;

class EnhancedImage extends FileFieldHandler implements FieldValueConverterInterface
{
    /**
     * Creates a value object to use as the field value when setting an image field type.
     *
     * @param array|string $fieldValue The path to the file or an array with 'path' and 'alt_text' keys
     * @param array        $context    The context for execution of the current migrations.
     *                                 Contains f.e. the path to the migration
     *
     * @throws InvalidArgumentType
     *
     * @todo resolve refs more
     */
    public function hashToFieldValue($fieldValue, array $context = []): EnhancedImageValue
    {
        $altText = '';
        $fileName = '';

        if (null === $fieldValue) {
            return new EnhancedImageValue();
        }

        if (is_string($fieldValue)) {
            $filePath = $fieldValue;
        } else {
            $filePath = $fieldValue['path'];
            if (isset($fieldValue['alt_text'])) {
                $altText = $fieldValue['alt_text'];
            }
            if (isset($fieldValue['filename'])) {
                $fileName = $fieldValue['filename'];
            }
        }

        // default format: path is relative to the 'images' dir
        $realFilePath = dirname($context['path']).'/images/'.$filePath;

        // but in the past, when using a string,
        // this worked as well as an absolute path, so we have to support it as well
        if (!is_file($realFilePath) && is_file($filePath)) {
            $realFilePath = $filePath;
        }

        $focusPoint = null;
        if (
            isset($fieldValue['focuspoint']) &&
            is_array($fieldValue['focuspoint']) &&
            2 === count($fieldValue['focuspoint'])
        ) {
            $focusPoint = new FocusPoint(
                (float) $fieldValue['focuspoint'][0],
                (float) $fieldValue['focuspoint'][1]
            );
        }

        return new EnhancedImageValue(
            [
                'path' => $realFilePath,
                'fileSize' => filesize($realFilePath),
                'fileName' => '' !== $fileName ? $fileName : basename($realFilePath),
                'alternativeText' => $altText,
                'focusPoint' => $focusPoint,
            ]
        );
    }

    /**
     * @param EnhancedImageValue $fieldValue
     *
     * @todo check out if this works in ezplatform
     */
    public function fieldValueToHash($fieldValue, array $context = []): array
    {
        if (null === $fieldValue->uri) {
            return null;
        }

        return [
            'path' => sprintf(
                '%s/%s',
                realpath($this->ioRootDir),
                ($this->ioDecorator ? $this->ioDecorator->undecorate($fieldValue->uri) : $fieldValue->uri)
            ),
            'filename' => $fieldValue->fileName,
            'alternativeText' => $fieldValue->alternativeText,
            'focuspoint' => [
                $fieldValue->focusPoint->getPosX(),
                $fieldValue->focusPoint->getPosY(),
            ],
        ];
    }
}
