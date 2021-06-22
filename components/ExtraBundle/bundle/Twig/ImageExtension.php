<?php

/**
 * NovaeZExtraBundle ImageExtension.
 *
 * @package   Novactive\Bundle\eZExtraBundle
 *
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZExtraBundle\Twig;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\FieldType\Image\Value as ImageValue;
use eZ\Publish\Core\FieldType\ImageAsset\Value as ImageAssetValue;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\SPI\Variation\VariationHandler;
use Novactive\Bundle\eZExtraBundle\Core\Helper\eZ\WrapperFactory;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ImageExtension extends AbstractExtension
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var ConfigResolverInterface
     */
    private $configResolver;

    /**
     * @var bool
     */
    private $enablePlaceholder;

    /**
     * @var bool
     */
    private $forcePlaceholder;

    /**
     * @var VariationHandler
     */
    private $variationHandler;

    /**
     * @var WrapperFactory
     */
    private $wrapperFactory;

    public function __construct(
        bool $enableImagePlaceholder,
        Environment $twig,
        ConfigResolverInterface $configResolver,
        VariationHandler $variationHandler,
        WrapperFactory $wrapperFactory
    ) {
        $this->enablePlaceholder = $enableImagePlaceholder;
        $this->twig = $twig;
        $this->configResolver = $configResolver;
        $forcePlaceholder = (bool) ($_SERVER['CONTINUOUS_INTEGRATION'] ?? false);
        $this->forcePlaceholder = $forcePlaceholder;
        $this->variationHandler = $variationHandler;
        $this->wrapperFactory = $wrapperFactory;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'get_image_tag',
                [$this, 'getImageTag'],
                [
                    'needs_environment' => false,
                    'is_safe' => ['html'],
                ]
            ),
            new TwigFunction(
                'get_image_url',
                [$this, 'getImageUrl'],
                [
                    'needs_environment' => false,
                    'is_safe' => ['html'],
                ]
            ),
            new TwigFunction(
                'get_image_asset_content',
                [$this, 'getImageAssetContent'],
                [
                    'needs_environment' => false,
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }

    private function getPlaceholderUrl(array $params, string $identifier, string $alias = 'original'): string
    {
        if (!isset($params['placeholder']) || false === $this->enablePlaceholder) {
            return '';
        }

        $width = $params['placeholder']['width'];
        $height = $params['placeholder']['height'] ?? null;
        if (null === $height) {
            return "https://via.placeholder.com/{$width}.png?text={$identifier}:{$alias} - {$width}";
        }

        return "https://via.placeholder.com/{$width}x{$height}.png?text={$identifier}:{$alias} - {$width}x{$height}";
    }

    private function getPlaceholderTag(array $params, string $identifier, string $alias = 'original'): string
    {
        if (!isset($params['placeholder']) || false === $this->enablePlaceholder) {
            return '';
        }

        $pictureIt = static function (string $tag): string {
            return "<picture>{$tag}</picture>";
        };

        $placeholderUrl = $this->getPlaceholderUrl($params, $identifier, $alias);
        $classes = implode(' ', $params['classes'] ?? []);
        if (null === $placeholderUrl) {
            return $pictureIt("<img src='//:0' alt='%s' class='{$classes}'/>");
        }

        $width = $params['placeholder']['width'];
        $height = $params['placeholder']['height'] ?? null;

        if (null === $height) {
            return $pictureIt("<img src='{$placeholderUrl}' width='{$width}' alt='%s' class='{$classes}'/>");
        }

        return $pictureIt(
            "<img src='{$placeholderUrl}' width='{$width}' height='{$height}' alt='%s' class='{$classes}'/>"
        );
    }

    private function fillPlaceholderForAlias(array &$params, string $alias): void
    {
        if (!isset($params['placeholder'])) {
            // check if we have a variation Fastly and get the retina
            $imageVariationsList = $this->configResolver->getParameter('image_variations');
            $variation = $imageVariationsList[$alias] ?? $imageVariationsList['default_placeholder'];

            $params['placeholder'] = [
                'width' => $variation['filters']['geometry/scaledownonly'][0],
                'height' => $variation['filters']['geometry/scaledownonly'][1],
            ];
        }
    }

    public function getImageUrl(
        ?Content $content,
        string $identifier,
        string $alias = 'optimized_original',
        ?array $params = []
    ): string {
        $this->fillPlaceholderForAlias($params, $alias);
        $placeholder = $this->getPlaceholderUrl($params, $identifier, $alias);

        if (null === $content || $this->forcePlaceholder) {
            return $placeholder;
        }

        $field = $content->getField($identifier);
        if (null === $field) {
            return $placeholder;
        }

        if ($field->value instanceof ImageAssetValue && $field->value->destinationContentId > 0) {
            $content = $this->wrapperFactory->createByContentId((int) $field->value->destinationContentId)->content;
            $identifier = 'image';
        }

        $field = $content->getField($identifier);
        if (null === $field) {
            return $placeholder;
        }

        if (!$field->value instanceof ImageValue) {
            return $placeholder;
        }

        return $this->variationHandler->getVariation($field, $content->versionInfo, $alias)->uri;
    }

    public function getImageTag(
        ?Content $content,
        string $identifier,
        string $alias = 'optimized_original',
        ?array $params = []
    ): string {
        $this->fillPlaceholderForAlias($params, $alias);
        $placeholderTag = $this->getPlaceholderTag($params, $identifier, $alias);

        if (null === $content || $this->forcePlaceholder) {
            return $placeholderTag;
        }

        $field = $content->getField($identifier);
        if (null === $field) {
            return sprintf($placeholderTag, "{$content->contentInfo->name}(#{$content->id}) on {$identifier}");
        }

        if ($field->value instanceof ImageAssetValue && $field->value->destinationContentId > 0) {
            $content = $this->wrapperFactory->createByContentId((int) $field->value->destinationContentId)->content;
            $identifier = 'image';
        }

        $field = $content->getField($identifier);
        if (null === $field) {
            return sprintf($placeholderTag, "{$content->contentInfo->name}(#{$content->id}) on {$identifier}");
        }

        if (!$field->value instanceof ImageValue) {
            return sprintf($placeholderTag, "{$content->contentInfo->name}(#{$content->id}) on {$identifier}");
        }

        return $this->twig->render(
            '@ezdesign/fields/display_image_tag.html.twig',
            [
                'content' => $content,
                'identifier' => $identifier,
                'alias' => $alias,
                'classes' => $params['classes'] ?? [],
            ]
        );
    }

    public function getImageAssetContent(Field $field): Content
    {
        return $this->wrapperFactory->createByContentId((int) $field->value->destinationContentId)->content;
    }
}
