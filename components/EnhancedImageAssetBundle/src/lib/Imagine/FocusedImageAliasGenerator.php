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

use Ibexa\Bundle\Core\Imagine\IORepositoryResolver;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidVariationException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Variation\Values\ImageVariation;
use Ibexa\Contracts\Core\Variation\VariationHandler;
use Ibexa\Core\FieldType\Image\Value as ImageValue;
use Ibexa\Core\FieldType\ImageAsset\Value as ImageAssetValue;
use Ibexa\Core\MVC\Exception\SourceImageNotFoundException;
use Imagine\Image\Box;
use InvalidArgumentException;
use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration;
use Novactive\EzEnhancedImageAsset\FieldType\EnhancedImage\FocusPoint;
use Novactive\EzEnhancedImageAsset\FieldType\EnhancedImage\Value as EnhancedImageValue;
use Novactive\EzEnhancedImageAsset\FocusPoint\FocusPointCalculator;
use Novactive\EzEnhancedImageAsset\Values\FocusedVariation;
use ReflectionClass;
use ReflectionException;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Core\FieldType\ImageAsset\AssetMapper;

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

    /** @var ContentService */
    private $contentService;

    /** @var AssetMapper */
    private $assetMapper;

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
     * @required
     */
    public function setContentService(ContentService $contentService): void
    {
        $this->contentService = $contentService;
    }

    /**
     * @required
     */
    public function setAssetMapper(AssetMapper $assetMapper): void
    {
        $this->assetMapper = $assetMapper;
    }
    /**
     * {@inheritdoc}
     *
     * if field value is not an instance of \Ibexa\Core\FieldType\Image\Value
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
        $focusPoint = null;
        if ($field->value instanceof ImageAssetValue && null !== $field->value->destinationContentId) {
            $destinationContent = $this->contentService->loadContent((int) $field->value->destinationContentId);
            $field = $this->assetMapper->getAssetField($destinationContent);
        }

        if ($field->value instanceof EnhancedImageValue) {
            /** @var FocusPoint $focusPoint */
            $focusPoint = $field->value->focusPoint;
        } elseif (
            $field->value instanceof ImageValue &&
            isset($field->value->additionalData['focalPointX']) &&
            isset($field->value->additionalData['focalPointY'])
        ) {
            $focusPoint = new FocusPoint(
                (float) $field->value->additionalData['focalPointX'] - ($field->value->width/2),
                (float) $field->value->additionalData['focalPointY'] - ($field->value->height/2),
            );
        } elseif (IORepositoryResolver::VARIATION_ORIGINAL !== $variationName) {
            $variationConfig = $this->filterConfiguration->get($variationName);
            if (isset($variationConfig['filters']['focusedThumbnail']['focus'])) {
                $focusPoint = new FocusPoint(...$variationConfig['filters']['focusedThumbnail']['focus']);
            }
        }
        $isFocusedVariation = false;
        if (IORepositoryResolver::VARIATION_ORIGINAL !== $variationName) {
            $variationConfig = $this->filterConfiguration->get($variationName);
            $isFocusedVariation = $focusPoint && isset($variationConfig['filters']['focusedThumbnail']);
            if ($isFocusedVariation) {
                $parameters = [
                    'filters' => [
                        'focusedThumbnail' => [
                            'focusPoint' => $focusPoint,
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
        $array = [];
        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
            $array[$property->getName()] = $property->getValue($variation);
            $property->setAccessible(false);
        }
        $array['focusPoint'] = $focusPoint;

        return new FocusedVariation($array);
    }
}
