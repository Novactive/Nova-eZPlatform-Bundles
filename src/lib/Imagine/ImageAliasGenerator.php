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
use eZ\Publish\Core\FieldType\Image\Value as ImageValue;
use eZ\Publish\Core\MVC\Exception\SourceImageNotFoundException;
use eZ\Publish\SPI\FieldType\Value;
use eZ\Publish\SPI\Variation\Values\ImageVariation;
use eZ\Publish\SPI\Variation\VariationHandler;
use Imagine\Exception\RuntimeException;
use InvalidArgumentException;
use Liip\ImagineBundle\Binary\Loader\LoaderInterface;
use Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException;
use Liip\ImagineBundle\Exception\Imagine\Cache\Resolver\NotResolvableException;
use Liip\ImagineBundle\Imagine\Cache\Resolver\ResolverInterface;
use Novactive\EzEnhancedImageAsset\Imagine\Filter\AliasFilterManager;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SplFileInfo;

/**
 * Class AliasGenerator.
 *
 * @package Novactive\EzEnhancedImageAsset\Imagine
 *
 * Copy of eZ\Bundle\EzPublishCoreBundle\Imagine\AliasGenerator to override the getVariation method
 * to pass the $runtimeFiltersConfig to the filterManager
 */
class ImageAliasGenerator implements VariationHandler
{
    public const ALIAS_ORIGINAL = 'original';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Loader used to retrieve the original image.
     * DataManager is not used to remain independent from ImagineBundle configuration.
     *
     * @var LoaderInterface
     */
    private $dataLoader;

    /**
     * @var AliasFilterManager
     */
    private $filterManager;

    /**
     * @var ResolverInterface
     */
    private $ioResolver;

    /**
     * ImageAliasGenerator constructor.
     */
    public function __construct(
        LoaderInterface $dataLoader,
        AliasFilterManager $filterManager,
        ResolverInterface $ioResolver,
        LoggerInterface $logger = null
    ) {
        $this->dataLoader    = $dataLoader;
        $this->filterManager = $filterManager;
        $this->ioResolver    = $ioResolver;
        $this->logger        = $logger ?? new NullLogger();
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
     */
    public function getVariation(Field $field, VersionInfo $versionInfo, $variationName, array $parameters = [])
    {
        /** @var ImageValue $imageValue */
        $imageValue         = $field->value;
        $fieldId            = $field->id;
        $fieldDefIdentifier = $field->fieldDefIdentifier;
        if (!$this->supportsValue($imageValue)) {
            $message = "Value for field #$fieldId ($fieldDefIdentifier) cannot be used for image alias generation.";
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
                throw new SourceImageNotFoundException($originalPath, 0, $e);
            }

            $this->logger->debug(
                "Generating '$variationName' variation on $originalPath, field #$fieldId ($fieldDefIdentifier)"
            );

            $this->ioResolver->store(
                $this->filterManager->applyFilter(
                    $originalBinary,
                    $variationName,
                    ['filters' => $parameters['filters'] ?? []]
                ),
                $originalPath,
                $variationName
            );
        } else {
            if (IORepositoryResolver::VARIATION_ORIGINAL === $variationName) {
                $variationWidth  = $imageValue->width;
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
                'name'     => $variationName,
                'fileName' => $aliasInfo->getFilename(),
                'dirPath'  => $aliasInfo->getPath(),
                'uri'      => $aliasInfo->getPathname(),
                'imageId'  => $imageValue->imageId,
                'width'    => $variationWidth,
                'height'   => $variationHeight,
            ]
        );
    }

    public function supportsValue(Value $value): bool
    {
        return $value instanceof ImageValue;
    }
}
