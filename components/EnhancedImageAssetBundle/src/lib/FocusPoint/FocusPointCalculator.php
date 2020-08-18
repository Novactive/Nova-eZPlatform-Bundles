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

namespace Novactive\EzEnhancedImageAsset\FocusPoint;

use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
use Imagine\Image\Point;
use Novactive\EzEnhancedImageAsset\FieldType\EnhancedImage\FocusPoint;

class FocusPointCalculator
{
    public function calculateCropFocusPoint(
        BoxInterface $imageSize,
        BoxInterface $cropSize,
        FocusPoint $focusPoint
    ): FocusPoint {
        $ratios = [
            $cropSize->getWidth() / $imageSize->getWidth(),
            $cropSize->getHeight() / $imageSize->getHeight(),
        ];

        $resizeRation = max($ratios);
        $resizedSize = $imageSize->scale($resizeRation);

        $cropStartPoint = $this->calculateCropStartPoint($resizedSize, $cropSize, $focusPoint);
        $imageFocusPoint = $this->toPixel($focusPoint, $resizedSize);

        return $this->toCoordinate(
            new FocusPoint(
                $imageFocusPoint->getPosX() - $cropStartPoint->getX(),
                $imageFocusPoint->getPosY() - $cropStartPoint->getY()
            ),
            $cropSize
        );
    }

    public function calculateCropStartPoint(
        BoxInterface $imageSize,
        BoxInterface $cropSize,
        FocusPoint $focusPoint
    ): Point {
        $imageFocusPoint = $this->toPixel($focusPoint, $imageSize);
        $posX = $imageFocusPoint->getPosX() - ($cropSize->getWidth() / 2);
        $posY = $imageFocusPoint->getPosY() - ($cropSize->getHeight() / 2);
        if ($posX < 0) {
            $posX = 0;
        }
        if ($posX + $cropSize->getWidth() > $imageSize->getWidth()) {
            $posX = $imageSize->getWidth() - $cropSize->getWidth();
        }
        if ($posY < 0) {
            $posY = 0;
        }
        if ($posY + $cropSize->getHeight() > $imageSize->getHeight()) {
            $posY = $imageSize->getHeight() - $cropSize->getHeight();
        }

        return new Point((int) $posX, (int) $posY);
    }

    protected function toCoordinate(FocusPoint $focusPoint, BoxInterface $imageSize): FocusPoint
    {
        return new FocusPoint(
            round(($focusPoint->getPosX() / $imageSize->getWidth() - .5) * 2, 2),
            round(($focusPoint->getPosY() / $imageSize->getHeight() - .5) * -2, 2)
        );
    }

    protected function toPixel(FocusPoint $focusPoint, BoxInterface $imageSize): FocusPoint
    {
        $percentFocusPoint = $this->toPercent($focusPoint);

        return new FocusPoint(
            $percentFocusPoint->getPosX() * $imageSize->getWidth() / 100,
            $percentFocusPoint->getPosY() * $imageSize->getHeight() / 100
        );
    }

    protected function toPercent(FocusPoint $focusPoint): FocusPoint
    {
        return new FocusPoint(
            (($focusPoint->getPosX() + 1) / 2) * 100,
            ((-$focusPoint->getPosY() + 1) / 2) * 100
        );
    }

    public function calculateCropSize(BoxInterface $imageSize, array $options): ?Box
    {
        $width = $options['size'][0] ?? null;
        $height = $options['size'][1] ?? null;

        $origWidth = $imageSize->getWidth();
        $origHeight = $imageSize->getHeight();

        if (null === $width || null === $height) {
            if (null === $height) {
                $height = (int) (($width / $origWidth) * $origHeight);
            } elseif (null === $width) {
                $width = (int) (($height / $origHeight) * $origWidth);
            }
        }

        $cropSize = new Box($width, $height);
        if ($cropSize->contains($imageSize)) {
            return null;
        }

        if (!$imageSize->contains($cropSize)) {
            $cropSize = new Box(
                min($imageSize->getWidth(), $cropSize->getWidth()),
                min($imageSize->getHeight(), $cropSize->getHeight())
            );
        }

        return $cropSize;
    }
}
