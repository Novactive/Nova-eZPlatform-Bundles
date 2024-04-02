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
use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidVariationException;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Variation\Values\ImageVariation;
use Ibexa\Contracts\Core\Variation\VariationHandler;
use Ibexa\Core\FieldType\Image\Value as ImageValue;
use Ibexa\Core\MVC\Exception\SourceImageNotFoundException;
use Imagine\Exception\RuntimeException;
use InvalidArgumentException;
use Liip\ImagineBundle\Binary\BinaryInterface;
use Liip\ImagineBundle\Binary\Loader\LoaderInterface;
use Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException;
use Liip\ImagineBundle\Exception\Imagine\Cache\Resolver\NotResolvableException;
use Liip\ImagineBundle\Imagine\Cache\Resolver\ResolverInterface;
use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SplFileInfo;

/**
 * Class AliasGenerator.
 *
 * @package Novactive\EzEnhancedImageAsset\Imagine
 *
 * Copy of Ibexa\Bundle\Core\Imagine\AliasGenerator to override the getVariation method
 * to pass the $runtimeFiltersConfig to the filterManager
 */
class ImageAliasGenerator implements VariationHandler
{
    public const ALIAS_ORIGINAL = 'original';

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /**
     * Loader used to retrieve the original image.
     * DataManager is not used to remain independent from ImagineBundle configuration.
     *
     * @var \Liip\ImagineBundle\Binary\Loader\LoaderInterface
     */
    private $dataLoader;

    /** @var \Liip\ImagineBundle\Imagine\Filter\FilterManager */
    private $filterManager;

    /** @var \Novactive\EzEnhancedImageAsset\Imagine\Filter\FilterConfiguration */
    private $filterConfiguration;

    /** @var \Liip\ImagineBundle\Imagine\Cache\Resolver\ResolverInterface */
    private $ioResolver;

    /**
     * ImageAliasGenerator constructor.
     */
    public function __construct(
        LoaderInterface $dataLoader,
        FilterManager $filterManager,
        ResolverInterface $ioResolver,
        FilterConfiguration $filterConfiguration,
        LoggerInterface $logger = null
    ) {
        $this->dataLoader = $dataLoader;
        $this->filterManager = $filterManager;
        $this->ioResolver = $ioResolver;
        $this->filterConfiguration = $filterConfiguration;
        $this->logger = null !== $logger ? $logger : new NullLogger();
    }

    /**
     * {@inheritdoc}
     */
    public function getVariation(Field $field, VersionInfo $versionInfo, $variationName, array $parameters = [])
    {
        /** @var \Ibexa\Core\FieldType\Image\Value $imageValue */
        $imageValue = $field->value;
        $fieldId = $field->id;
        $fieldDefIdentifier = $field->fieldDefIdentifier;
        if (!$this->supportsValue($imageValue)) {
            $message = "Value of Field with ID $fieldId ($fieldDefIdentifier) 
            cannot be used for generating an image variation.";
            throw new InvalidArgumentException($message);
        }

        $originalPath = $imageValue->id;

        $variationWidth = $variationHeight = null;
        // Create the image alias only if it does not already exist.
        if (
            IORepositoryResolver::VARIATION_ORIGINAL !== $variationName
            && !$this->ioResolver->isStored($originalPath, $variationName)
        ) {
            try {
                $originalBinary = $this->dataLoader->find($originalPath);
            } catch (NotLoadableException $e) {
                throw new SourceImageNotFoundException((string) $originalPath, 0, $e);
            }

            $this->logger->debug(
                "Generating '$variationName' variation on $originalPath, field #$fieldId ($fieldDefIdentifier)"
            );

            $this->ioResolver->store(
                $this->applyFilter($originalBinary, $variationName, ['filters' => $parameters['filters'] ?? []]),
                $originalPath,
                $variationName
            );
        } else {
            if (IORepositoryResolver::VARIATION_ORIGINAL === $variationName) {
                $variationWidth = $imageValue->width;
                $variationHeight = $imageValue->height;
            }
            $this->logger->debug(
                "'$variationName' variation on $originalPath is already generated. Loading from cache."
            );
        }

        try {
            $aliasInfo = new SplFileInfo(
                $this->ioResolver->resolve($originalPath, $variationName)
            );
        } catch (NotResolvableException $e) {
            // If for some reason image alias cannot be resolved, throw the appropriate exception.
            throw new InvalidVariationException($variationName, 'image', 0, $e);
        } catch (RuntimeException $e) {
            throw new InvalidVariationException($variationName, 'image', 0, $e);
        }

        return new ImageVariation(
            [
                'name' => $variationName,
                'fileName' => $aliasInfo->getFilename(),
                'dirPath' => $aliasInfo->getPath(),
                'uri' => $aliasInfo->getPathname(),
                'imageId' => $imageValue->imageId,
                'width' => $variationWidth,
                'height' => $variationHeight,
            ]
        );
    }

    /**
     * Applies $variationName filters on $image.
     *
     * Both variations configured in Ibexa (SiteAccess context) and LiipImagineBundle are used.
     * An Ibexa variation may have a "reference".
     * In that case, reference's filters are applied first, recursively (a reference may also have another reference).
     * Reference must be a valid variation name, configured in Ibexa or in LiipImagineBundle.
     *
     * @return \Liip\ImagineBundle\Binary\BinaryInterface
     */
    private function applyFilter(BinaryInterface $image, string $variationName, array $runtimeFiltersConfig = [])
    {
        $filterConfig = $this->filterConfiguration->get($variationName);
        // If the variation has a reference, we recursively call this method to apply reference's filters.
        if (
            isset($filterConfig['reference']) &&
            IORepositoryResolver::VARIATION_ORIGINAL !== $filterConfig['reference']
        ) {
            $image = $this->applyFilter($image, $filterConfig['reference'], $runtimeFiltersConfig);
        }

        if (!isset($filterConfig['filters']['focusedThumbnail'])) {
            unset($runtimeFiltersConfig['filters']['focusedThumbnail']);
        }

        return $this->filterManager->applyFilter($image, $variationName, $runtimeFiltersConfig);
    }

    public function supportsValue(Value $value): bool
    {
        return $value instanceof ImageValue;
    }
}
