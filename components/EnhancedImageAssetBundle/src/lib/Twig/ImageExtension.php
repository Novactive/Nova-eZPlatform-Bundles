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

use eZ\Publish\API\Repository\Exceptions\InvalidVariationException;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Exception\SourceImageNotFoundException;
use eZ\Publish\SPI\Variation\Values\ImageVariation;
use InvalidArgumentException;
use Liip\ImagineBundle\Exception\Imagine\Filter\NonExistingFilterException;
use Novactive\EzEnhancedImageAsset\Imagine\FocusedImageAliasGenerator;
use Novactive\EzEnhancedImageAsset\Values\FocusedVariation;
use Psr\Log\LoggerInterface;
use ReflectionException;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

class ImageExtension extends AbstractExtension implements GlobalsInterface
{
    /** @var FocusedImageAliasGenerator */
    protected $focusedImageAliasGenerator;

    /** @var LoggerInterface */
    protected $logger;

    /** @var AssetExtension */
    protected $assetExtension;

    /** @var ConfigResolverInterface */
    protected $configResolver;

    public function getGlobals(): array
    {
        return [
            'lazy_load_images' => $this->configResolver->getParameter('enable_lazy_load', 'ez_enhanced_image_asset'),
            'enable_retina_variations' => $this->configResolver->getParameter('enable_retina', 'ez_enhanced_image_asset')
        ];
    }

    /**
     * @required
     */
    public function setFocusedImageAliasGenerator(FocusedImageAliasGenerator $focusedImageAliasGenerator): void
    {
        $this->focusedImageAliasGenerator = $focusedImageAliasGenerator;
    }

    /**
     * @required
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @required
     */
    public function setAssetExtension(AssetExtension $assetExtension): void
    {
        $this->assetExtension = $assetExtension;
    }

    /**
     * @required
     */
    public function setConfigResolver(ConfigResolverInterface $configResolver): void
    {
        $this->configResolver = $configResolver;
    }

    protected function isVariationsAvailable($variationName): bool
    {
        $configuredVariations = $this->configResolver->getParameter('image_variations');

        return isset($configuredVariations[$variationName]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ezpublish.image';
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
        $attrs = $parameters['attrs'] ?? [];

        $this->initiateArrayAttribute($attrs, 'srcset');
        $this->initiateArrayAttribute($attrs, 'class');
        $attrs['class'][] = 'enhancedimage--img--lazyload';

        $defaultVariation = $this->appendDefaultVariationAttrs($field, $versionInfo, $variationName, $attrs);
        if ($defaultVariation && $retinaSupportEnabled) {
            $this->appendRetinaVariationAttrs($field, $versionInfo, $variationName, $defaultVariation, $attrs);
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
     * @param       $variationName
     * @param array $attrs
     *
     * @throws ReflectionException
     *
     * @return ImageVariation|FocusedVariation|null
     */
    protected function appendDefaultVariationAttrs(
        Field $field,
        VersionInfo $versionInfo,
        $variationName,
        &$attrs = []
    ) {
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
     * @return ImageVariation|FocusedVariation|null
     */
    public function getImageVariation(Field $field, VersionInfo $versionInfo, string $variationName)
    {
        if (!$this->isVariationsAvailable($variationName)) {
            return null;
        }
        try {
            return $this->focusedImageAliasGenerator->getVariation($field, $versionInfo, $variationName);
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

    /**
     * @param       $variationName
     * @param array $attrs
     *
     * @throws ReflectionException
     *
     * @return ImageVariation|FocusedVariation|null
     */
    protected function appendRetinaVariationAttrs(
        Field $field,
        VersionInfo $versionInfo,
        $variationName,
        ImageVariation $defaultVariation,
        &$attrs = []
    ) {
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
