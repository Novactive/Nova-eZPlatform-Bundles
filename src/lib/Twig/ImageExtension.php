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

use eZ\Publish\API\Repository\Exceptions\InvalidVariationException;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\MVC\Exception\SourceImageNotFoundException;
use eZ\Publish\SPI\Variation\Values\ImageVariation;
use InvalidArgumentException;
use Liip\ImagineBundle\Exception\Imagine\Filter\NonExistingFilterException;
use Novactive\EzEnhancedImageAsset\Imagine\FocusedImageAliasGenerator;
use Novactive\EzEnhancedImageAsset\Values\FocusedVariation;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Twig_Extension;
use Twig_SimpleFunction;

class ImageExtension extends Twig_Extension
{
    /** @var FocusedImageAliasGenerator */
    protected $focusedImageAliasGenerator;

    /** @var LoggerInterface */
    protected $logger;

    /** @var AssetExtension */
    protected $assetExtension;

    /**
     * @param FocusedImageAliasGenerator $focusedImageAliasGenerator
     * @required
     */
    public function setFocusedImageAliasGenerator(FocusedImageAliasGenerator $focusedImageAliasGenerator): void
    {
        $this->focusedImageAliasGenerator = $focusedImageAliasGenerator;
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

    /**
     * @param AssetExtension $assetExtension
     * @required
     */
    public function setAssetExtension(AssetExtension $assetExtension): void
    {
        $this->assetExtension = $assetExtension;
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
            new Twig_SimpleFunction(
                'ez_focused_image_alias',
                [$this, 'getImageVariation'],
                ['is_safe' => ['html']]
            ),
            new Twig_SimpleFunction(
                'ez_image_attrs',
                [$this, 'getImageAttributes'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    /**
     * @param Field       $field
     * @param VersionInfo $versionInfo
     * @param $variationName
     * @param array $parameters
     *
     * @throws \ReflectionException
     *
     * @return array|mixed
     */
    public function getImageAttributes(Field $field, VersionInfo $versionInfo, $variationName, $parameters = [])
    {
        $lazyLoadEnabled      = $parameters['lazyLoad'] ?? false;
        $retinaSupportEnabled = $parameters['retina'] ?? false;
        $attrs                = $parameters['attrs'] ?? [];

        if (!isset($attrs['class'])) {
            $attrs['class'] = [];
        } else {
            $attrs['class'] = !is_array($attrs['class']) ? [$attrs['class']] : $attrs['class'];
        }

        $defaultVariation = $this->getImageVariation($field, $versionInfo, $variationName);
        if ($defaultVariation instanceof FocusedVariation && $defaultVariation->focusPoint) {
            $attrs['data-focus-x'] = $defaultVariation->focusPoint->getPosX();
            $attrs['data-focus-y'] = $defaultVariation->focusPoint->getPosY();
            $attrs['class'][]      = 'enhancedimage--focused-img';
        }
        $attrs['srcset'] = str_replace(' ', '%20', $this->assetExtension->getAssetUrl($defaultVariation->uri));

        try {
            if ($retinaSupportEnabled &&
                $retinaVariation = $this->getImageVariation($field, $versionInfo, "{$variationName}_retina")) {
                $retinaUri = str_replace(' ', '%20', $this->assetExtension->getAssetUrl($retinaVariation->uri));

                $attrs['srcset'] .= ", {$retinaUri} 2x";
            }
        } catch (NonExistingFilterException $e) {
            $this->logger->warning($e->getMessage());
        }
        if ($lazyLoadEnabled &&
            $placeholderVariation = $this->getImageVariation($field, $versionInfo, 'placeholder')) {
            $attrs['class'][]     = 'enhancedimage--img--lazyload';
            $attrs['class'][]     = 'blur-up';
            $attrs['data-srcset'] = $attrs['srcset'];
            $attrs['srcset']      = str_replace(
                ' ',
                '%20',
                $this->assetExtension->getAssetUrl($placeholderVariation->uri)
            );
        }

        $attrs['class'] = implode(' ', $attrs['class']);

        return $attrs;
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
}
