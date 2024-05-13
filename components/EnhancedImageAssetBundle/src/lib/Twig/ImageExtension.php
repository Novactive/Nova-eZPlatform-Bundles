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

namespace Novactive\EzEnhancedImageAsset\Twig;

use Ibexa\Bundle\Core\Imagine\IORepositoryResolver;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidVariationException;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Core\Variation\Values\ImageVariation;
use Ibexa\Contracts\Core\Variation\Values\Variation;
use Ibexa\Core\MVC\Exception\SourceImageNotFoundException;
use InvalidArgumentException;
use Liip\ImagineBundle\Exception\Imagine\Filter\NonExistingFilterException;
use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration;
use Novactive\EzEnhancedImageAsset\Values\FocusedVariation;
use Psr\Log\LoggerInterface;
use ReflectionException;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

class ImageExtension extends AbstractExtension implements GlobalsInterface
{
    /** @var \Ibexa\Contracts\Core\Variation\VariationHandler */
    protected $imageVariationService;

    /** @var LoggerInterface */
    protected $logger;

    /** @var AssetExtension */
    protected $assetExtension;

    /** @var ConfigResolverInterface */
    protected $configResolver;

    /** @var FilterConfiguration */
    protected $filterConfiguration;

    public function __construct(
        \Ibexa\Contracts\Core\Variation\VariationHandler $imageVariationService,
        LoggerInterface $logger,
        AssetExtension $assetExtension,
        ConfigResolverInterface $configResolver,
        FilterConfiguration $filterConfiguration
    ) {
        $this->imageVariationService = $imageVariationService;
        $this->logger = $logger;
        $this->assetExtension = $assetExtension;
        $this->configResolver = $configResolver;
        $this->filterConfiguration = $filterConfiguration;
    }

    public function getGlobals(): array
    {
        return [
            'lazy_load_images' => $this->configResolver->getParameter(
                'enable_lazy_load',
                'ez_enhanced_image_asset'
            ),
            'enable_retina_variations' => $this->configResolver->getParameter(
                'enable_retina',
                'ez_enhanced_image_asset'
            ),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ibexa.image';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'ez_focused_image_alias',
                [$this, 'getImageVariation'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'ez_image_attrs',
                [$this, 'getImageAttributes'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    /**
     * @param       $variationName
     * @param array $parameters
     *
     * @throws ReflectionException
     *
     * @return array|mixed
     */
    public function getImageAttributes(Field $field, VersionInfo $versionInfo, $variationName, $parameters = [])
    {
        $lazyLoadEnabled = $parameters['lazyLoad'] ?? false;
        $retinaSupportEnabled = $parameters['retina'] ?? false;
        $addMimeType = $parameters['addMimeType'] ?? false;
        $attrs = $parameters['attrs'] ?? [];

        $this->initiateArrayAttribute($attrs, 'srcset');
        $this->initiateArrayAttribute($attrs, 'class');
        $attrs['class'][] = 'enhancedimage--img--lazyload';

        $defaultVariation = $this->appendDefaultVariationAttrs($field, $versionInfo, $variationName, $attrs);
        if ($defaultVariation && $retinaSupportEnabled) {
            $this->appendRetinaVariationAttrs($field, $versionInfo, $variationName, $defaultVariation, $attrs);
        }

        if ($addMimeType && $defaultVariation) {
            $attrs['type'] = $defaultVariation->mimeType;
        }
        if (is_array($attrs['srcset'])) {
            $attrs['srcset'] = implode(', ', $attrs['srcset']);
        }
        if ($lazyLoadEnabled) {
            $attrs['class'][] = 'has-placeholder';
            $attrs['data-srcset'] = is_array($attrs['srcset']) ? implode(', ', $attrs['srcset']) : $attrs['srcset'];
            unset($attrs['srcset']);
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

    /**
     * @param $variationName
     *
     *@throws ReflectionException
     *
     * @return ImageVariation|FocusedVariation|null
     */
    protected function appendDefaultVariationAttrs(
        Field $field,
        VersionInfo $versionInfo,
        string $variationName,
        array &$attrs = []
    ): ?ImageVariation {
        $defaultVariation = $this->getImageVariation($field, $versionInfo, $variationName);
        if (!$defaultVariation) {
            return null;
        }

        if ($defaultVariation instanceof FocusedVariation && $defaultVariation->focusPoint) {
            $attrs['data-focus-x'] = $defaultVariation->focusPoint->getPosX();
            $attrs['data-focus-y'] = $defaultVariation->focusPoint->getPosY();
            $attrs['class'][] = 'enhancedimage--focused-img';
        }
        $attrs['srcset'][] = str_replace(' ', '%20', $this->assetExtension->getAssetUrl($defaultVariation->uri));
        $attrs['data-width'] = $defaultVariation->width;
        $attrs['data-height'] = $defaultVariation->height;

        return $defaultVariation;
    }

    /**
     * Returns the image variation object for $field/$versionInfo.
     *
     * @throws ReflectionException
     *
     * @return \Ibexa\Contracts\Core\Variation\Values\Variation
     */
    public function getImageVariation(Field $field, VersionInfo $versionInfo, string $variationName): ?Variation
    {
        if (!$this->isVariationsAvailable($variationName)) {
            return null;
        }
        try {
            return $this->imageVariationService->getVariation($field, $versionInfo, $variationName);
        } catch (InvalidVariationException $e) {
            if (isset($this->logger)) {
                $this->logger->error(
                    "Couldn't get variation '{$variationName}' for image with id {$field->value->id}"
                );
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

    protected function isVariationsAvailable(string $variationName): bool
    {
        if (IORepositoryResolver::VARIATION_ORIGINAL === $variationName) {
            return true;
        }
        try {
            $this->filterConfiguration->get($variationName);

            return true;
        } catch (NonExistingFilterException $e) {
            return false;
        }
    }

    /**
     * @param $variationName
     *
     * @throws ReflectionException
     *
     * @return ImageVariation|FocusedVariation|null
     */
    protected function appendRetinaVariationAttrs(
        Field $field,
        VersionInfo $versionInfo,
        string $variationName,
        ImageVariation $defaultVariation,
        array &$attrs = []
    ): ?ImageVariation {
        try {
            $retinaVariation = $this->getImageVariation(
                $field,
                $versionInfo,
                "{$variationName}_retina"
            );
            if ($retinaVariation && $retinaVariation->width >= $defaultVariation->width * 2) {
                $retinaUri = str_replace(
                    ' ',
                    '%20',
                    $this->assetExtension->getAssetUrl($retinaVariation->uri)
                );

                $attrs['srcset'][] = "{$retinaUri} 2x";

                return $retinaVariation;
            }
        } catch (NonExistingFilterException $e) {
            $this->logger->warning($e->getMessage());
        }

        return null;
    }
}
