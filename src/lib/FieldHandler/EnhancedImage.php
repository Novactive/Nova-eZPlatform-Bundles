<?php
/**
 * @copyright Novactive
 * Date: 07/08/19
 */

declare(strict_types=1);

namespace Novactive\EzEnhancedImageAsset\FieldHandler;

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
     * @return EnhancedImageValue
     *
     * @todo resolve refs more
     */
    public function hashToFieldValue($fieldValue, array $context = [])
    {
        $altText  = '';
        $fileName = '';

        if (null === $fieldValue) {
            return new EnhancedImageValue();
        } elseif (is_string($fieldValue)) {
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
        if (isset($fieldValue['focuspoint']) &&
            is_array($fieldValue['focuspoint']) &&
            2 === count($fieldValue['focuspoint'])
        ) {
            $focusPoint = new FocusPoint(
                floatval($fieldValue['focuspoint'][0]),
                floatval($fieldValue['focuspoint'][1])
            );
        }

        return new EnhancedImageValue(
            [
                'path'            => $realFilePath,
                'fileSize'        => filesize($realFilePath),
                'fileName'        => '' != $fileName ? $fileName : basename($realFilePath),
                'alternativeText' => $altText,
                'focusPoint'      => $focusPoint,
            ]
        );
    }

    /**
     * @param EnhancedImageValue $fieldValue
     * @param array              $context
     *
     * @return array
     *
     * @todo check out if this works in ezplatform
     */
    public function fieldValueToHash($fieldValue, array $context = [])
    {
        if (null == $fieldValue->uri) {
            return null;
        }

        return [
            'path'            => sprintf(
                '%s/%s',
                realpath($this->ioRootDir),
                ($this->ioDecorator ? $this->ioDecorator->undecorate($fieldValue->uri) : $fieldValue->uri)
            ),
            'filename'        => $fieldValue->fileName,
            'alternativeText' => $fieldValue->alternativeText,
            'focuspoint'      => [
                $fieldValue->focusPoint->getPosX(),
                $fieldValue->focusPoint->getPosY(),
            ],
        ];
    }
}
