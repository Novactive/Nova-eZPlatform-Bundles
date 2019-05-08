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
use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration;
use Novactive\EzEnhancedImageAsset\FieldType\EnhancedImage\FocusPoint;
use Novactive\EzEnhancedImageAsset\FieldType\EnhancedImage\Value as EnhancedImageValue;
use Novactive\EzEnhancedImageAsset\FocusPoint\FocusPointCalculator;
use Novactive\EzEnhancedImageAsset\Values\FocusedVariation;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionException;
use Twig_Extension;
use Twig_SimpleFunction;

class ImageExtension extends Twig_Extension
{
    /** @var VariationHandler */
    protected $imageVariationService;

    /** @var FocusPointCalculator */
    protected $focusPointCalculator;

    /** @var FilterConfiguration */
    protected $filterConfiguration;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * @param VariationHandler $imageVariationService
     *
     * @required
     */
    public function setImageVariationService(VariationHandler $imageVariationService): void
    {
        $this->imageVariationService = $imageVariationService;
    }

    /**
     * @param FocusPointCalculator $focusPointCalculator
     *
     * @required
     */
    public function setFocusPointCalculator(FocusPointCalculator $focusPointCalculator): void
    {
        $this->focusPointCalculator = $focusPointCalculator;
    }

    /**
     * @param FilterConfiguration $filterConfiguration
     *
     * @required
     */
    public function setFilterConfiguration(FilterConfiguration $filterConfiguration): void
    {
        $this->filterConfiguration = $filterConfiguration;
    }

    /**
     * @param LoggerInterface $logger
     *
     * @required
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function getName(): string
    {
        return 'ezpublish.image';
    }

    public function getFunctions(): array
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
     * @param string      $variationName
     *
     * @throws ReflectionException
     *
     * @return ImageVariation|FocusedVariation|null
     */
    public function getImageVariation(Field $field, VersionInfo $versionInfo, string $variationName)
    {
        try {
            $parameters         = [];
            $isFocusedThumbnail = false;
            $focusPoint         = null;

            if (IORepositoryResolver::VARIATION_ORIGINAL !== $variationName) {
                $variationConfig    = $this->filterConfiguration->get($variationName);
                $isFocusedThumbnail = isset(
                    $variationConfig['filters'],
                    $variationConfig['filters']['focusedThumbnail']
                );
                if ($isFocusedThumbnail) {
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

            if (!$field->value instanceof EnhancedImageValue || !$isFocusedThumbnail) {
                return $variation;
            }

            if (IORepositoryResolver::VARIATION_ORIGINAL !== $variationName && null !== $focusPoint) {
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

        return null;
    }
}
