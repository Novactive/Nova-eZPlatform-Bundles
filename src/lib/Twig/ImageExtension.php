<?php
/**
 * NovaeZEnhancedImageAssetBundle.
 *
 * @package   NovaeZEnhancedImageAssetBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZEnhancedImageAssetBundle/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Novactive\EzEnhancedImageAsset\Twig;

use eZ\Bundle\EzPublishCoreBundle\Imagine\IORepositoryResolver;
use eZ\Publish\API\Repository\Exceptions\InvalidVariationException;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\MVC\Exception\SourceImageNotFoundException;
use eZ\Publish\SPI\Variation\Values\ImageVariation;
use eZ\Publish\SPI\Variation\VariationHandler;
use Imagine\Image\Box;
use InvalidArgumentException;
use Novactive\EzEnhancedImageAsset\FieldType\EnhancedImage\FocusPoint;
use Novactive\EzEnhancedImageAsset\FieldType\EnhancedImage\Value as EnhancedImageValue;
use Novactive\EzEnhancedImageAsset\FocusPoint\FocusPointCalculator;
use Novactive\EzEnhancedImageAsset\Values\FocusedVariation;
use ReflectionClass;
use Twig_Extension;
use Twig_SimpleFunction;

class ImageExtension extends Twig_Extension
{
    /** @var VariationHandler */
    protected $imageVariationService;

    /** @var FocusPointCalculator */
    protected $focusPointCalculator;

    /**
     * @param VariationHandler $imageVariationService
     * @required
     */
    public function setImageVariationService(VariationHandler $imageVariationService): void
    {
        $this->imageVariationService = $imageVariationService;
    }

    /**
     * @param FocusPointCalculator $focusPointCalculator
     * @required
     */
    public function setFocusPointCalculator(FocusPointCalculator $focusPointCalculator): void
    {
        $this->focusPointCalculator = $focusPointCalculator;
    }

    public function getName()
    {
        return 'ezpublish.image';
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction(
                'ez_focused_image_alias',
                [$this, 'getImageVariation'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    /**
     * Returns the image variation object for $field/$versionInfo.
     *
     * @param Field       $field
     * @param VersionInfo $versionInfo
     * @param $variationName
     *
     * @throws \ReflectionException
     *
     * @return ImageVariation|FocusedVariation
     */
    public function getImageVariation(Field $field, VersionInfo $versionInfo, $variationName)
    {
        try {
            /** @var FocusPoint $focusPoint */
            $focusPoint = $field->value->focusPoint;

            /** @var ImageVariation $variation */
            $variation = $this->imageVariationService->getVariation(
                $field,
                $versionInfo,
                $variationName,
                [
                    'filters' => [
                        'focusedThumbnail' => [
                            'focusPoint'   => $focusPoint,
                            'originalSize' => new Box($field->value->width, $field->value->height),
                        ],
                    ],
                ]
            );
            if (!$field->value instanceof EnhancedImageValue) {
                return $variation;
            }

            if (IORepositoryResolver::VARIATION_ORIGINAL !== $variationName) {
                /** @var ImageVariation $originalVariation */
                $originalVariation = $this->imageVariationService->getVariation(
                    $field,
                    $versionInfo,
                    IORepositoryResolver::VARIATION_ORIGINAL
                );

                $focusPoint = $this->focusPointCalculator->calculateCropFocusPoint(
                    new Box($originalVariation->width, $originalVariation->height),
                    new Box($variation->width, $variation->height),
                    $focusPoint
                );
            }

            $reflectionClass = new ReflectionClass(get_class($variation));
            $array           = [];
            foreach ($reflectionClass->getProperties() as $property) {
                $property->setAccessible(true);
                $array[$property->getName()] = $property->getValue($variation);
                $property->setAccessible(false);
            }
            $array['focusPoint'] = $focusPoint;

            return new FocusedVariation($array);
        } catch (InvalidVariationException $e) {
            if (isset($this->logger)) {
                $this->logger->error("Couldn't get variation '{$variationName}' for image with id {$field->value->id}");
            }
        } catch (SourceImageNotFoundException $e) {
            if (isset($this->logger)) {
                $this->logger->error(
                    "Couldn't create variation '{$variationName}' 
                    for image with id {$field->value->id} because source image can't be found"
                );
            }
        } catch (InvalidArgumentException $e) {
            if (isset($this->logger)) {
                $this->logger->error(
                    "Couldn't create variation '{$variationName}' 
                    for image with id {$field->value->id} because an image could not be created from the given input"
                );
            }
        }
    }
}
