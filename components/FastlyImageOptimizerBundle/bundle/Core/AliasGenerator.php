<?php

/**
 * Novactive eZ Fastly Image Optimizer Bundle.
 *
 * @author    Novactive <direction.technique@novactive.com>
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZFastlyImageOptimizerBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZFastlyImageOptimizerBundle\Core;

use Exception;
use eZ\Publish\API\Repository\Exceptions\InvalidVariationException;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Exception\SourceImageNotFoundException;
use eZ\Publish\SPI\Variation\Values\ImageVariation;
use eZ\Publish\SPI\Variation\VariationHandler as VariationService;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;

class AliasGenerator implements VariationService
{
    /**
     * @var ConfigResolverInterface
     */
    protected $configResolver;

    /**
     * @var VariationService
     */
    protected $variationService;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        VariationService $variationService,
        ConfigResolverInterface $configResolver,
        LoggerInterface $logger
    ) {
        $this->configResolver = $configResolver;
        $this->variationService = $variationService;
        $this->logger = $logger;
    }

    public function getVariation(
        Field $field,
        VersionInfo $versionInfo,
        $variationName,
        array $parameters = []
    ): ?ImageVariation {
        try {
            $eZVariationsList = $this->configResolver->getParameter('image_variations');
            $fastlyioVariationsList = $this->configResolver->getParameter('fastlyio_variations', 'nova_ezfastlyio');
            $fastlyioDisabled = $this->configResolver->getParameter('fastlyio_disabled', 'nova_ezfastlyio');

            // 1. if no fastlyio variation that matches just return the the eZ one.
            // this can crash there is no fallback needed.
            if ($fastlyioDisabled || !\array_key_exists($variationName, $fastlyioVariationsList)) {
                /** @var ImageVariation $variation */
                $variation = $this->variationService->getVariation($field, $versionInfo, $variationName, $parameters);

                return $variation;
            }

            // 2. if the reference does not exist we fallback on the original
            $eZVariationName = $fastlyioVariationsList[$variationName]['ezreference_variation'];
            if ('original' !== $eZVariationName && !\array_key_exists($eZVariationName, $eZVariationsList)) {
                $eZVariationName = 'original';
            }

            /** @var ImageVariation $variation */
            $variation = $this->variationService->getVariation($field, $versionInfo, $eZVariationName, $parameters);

            // 3. we apply fastly filters/params
            $filters = $fastlyioVariationsList[$variationName]['filters'];

            return new ImageVariation(
                [
                    'uri' => $variation->uri.'?'.http_build_query($filters),
                    'name' => $variation->name,
                    'imageId' => $variation->imageId,
                    'fileName' => $variation->fileName,
                    'dirPath' => $variation->dirPath,
                    // forward the asked width/height if in the filters
                    'width' => $filters['width'] ?? null,
                    'height' => $filters['height'] ?? null,
                ]
            );
        } catch (InvalidVariationException $exception) {
            $this->logger->error("Couldn't get variation '{$variationName}' for image with id {$field->value->id}");
        } catch (SourceImageNotFoundException $exception) {
            $this->logger->error(
                "Couldn't create variation '{$variationName}'".
                " for image with id {$field->value->id} because source image can't be found"
            );
        } catch (InvalidArgumentException $exception) {
            $this->logger->error(
                "Couldn't create variation '{$variationName}'".
                " for image with id {$field->value->id} because an image could not be created from the given input"
            );
        } catch (Exception $exception) {
            $this->logger->error(
                "Couldn't create variation '{$variationName}'".
                " for image with id {$field->value->id}: {$exception->getMessage()}"
            );
        }

        return null;
    }
}
