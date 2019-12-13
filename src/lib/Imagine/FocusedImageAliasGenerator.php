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

namespace Novactive\EzEnhancedImageAsset\Imagine;

use eZ\Bundle\EzPublishCoreBundle\Imagine\IORepositoryResolver;
use eZ\Publish\API\Repository\Exceptions\InvalidVariationException;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\MVC\Exception\SourceImageNotFoundException;
use eZ\Publish\SPI\Variation\Values\ImageVariation;
use eZ\Publish\SPI\Variation\VariationHandler;
use Imagine\Image\Box;
use InvalidArgumentException;
use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration;
use Novactive\EzEnhancedImageAsset\FieldType\EnhancedImage\FocusPoint;
use Novactive\EzEnhancedImageAsset\FieldType\EnhancedImage\Value as EnhancedImageValue;
use Novactive\EzEnhancedImageAsset\FocusPoint\FocusPointCalculator;
use Novactive\EzEnhancedImageAsset\Values\FocusedVariation;
use ReflectionClass;
use ReflectionException;

/**
 * Class FocusedImageAliasGenerator.
 *
 * @package Novactive\EzEnhancedImageAsset\Imagine
 */
class FocusedImageAliasGenerator implements VariationHandler
{
    /** @var VariationHandler */
    protected $imageVariationService;

    /** @var FocusPointCalculator */
    protected $focusPointCalculator;

    /** @var FilterConfiguration */
    protected $filterConfiguration;

    /**
     * @required
     */
    public function setImageVariationService(VariationHandler $imageVariationService): void
    {
        $this->imageVariationService = $imageVariationService;
    }

    /**
     * @required
     */
    public function setFocusPointCalculator(FocusPointCalculator $focusPointCalculator): void
    {
        $this->focusPointCalculator = $focusPointCalculator;
    }

    /**
     * @required
     */
    public function setFilterConfiguration(FilterConfiguration $filterConfiguration): void
    {
        $this->filterConfiguration = $filterConfiguration;
    }

    /**
     * {@inheritdoc}
     *
     * if field value is not an instance of \eZ\Publish\Core\FieldType\Image\Value
     *
     * @throws InvalidArgumentException
     *
     * if source image cannot be found
     * @throws SourceImageNotFoundException
     *
     * if a problem occurs with generated variation
     * @throws InvalidVariationException
     * @throws ReflectionException
     */
    public function getVariation(Field $field, VersionInfo $versionInfo, $variationName, array $parameters = [])
    {
        $isFocusedVariation = IORepositoryResolver::VARIATION_ORIGINAL !== $variationName
                              && $field->value instanceof EnhancedImageValue;
        $focusPoint         = null;

        if ($isFocusedVariation) {
            $variationConfig    = $this->filterConfiguration->get($variationName);
            $isFocusedVariation = isset($variationConfig['filters']['focusedThumbnail']);
            if ($isFocusedVariation) {
                /** @var FocusPoint $focusPoint */
                $focusPoint = $field->value->focusPoint;
                $parameters = [
                    'filters' => [
                        'focusedThumbnail' => [
                            'focusPoint'   => $focusPoint,
                            'originalSize' => new Box($field->value->width, $field->value->height),
                        ],
                    ],
                ];
            }
        }

        /** @var ImageVariation $variation */
        $variation = $this->imageVariationService->getVariation(
            $field,
            $versionInfo,
            $variationName,
            $parameters
        );

        if (!$isFocusedVariation) {
            return $variation;
        }

        if ($focusPoint) {
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
    }
}
