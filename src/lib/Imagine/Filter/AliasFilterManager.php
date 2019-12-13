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

namespace Novactive\EzEnhancedImageAsset\Imagine\Filter;

use eZ\Bundle\EzPublishCoreBundle\Imagine\IORepositoryResolver;
use Liip\ImagineBundle\Binary\BinaryInterface;
use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;

/**
 * NovaeZEnhancedImageAssetBundle.
 *
 * @package   NovaeZEnhancedImageAssetBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZEnhancedImageAssetBundle/blob/master/LICENSE
 */
class AliasFilterManager
{
    /**
     * @var FilterManager
     */
    protected $filterManager;

    /**
     * @var FilterConfiguration
     */
    protected $filterConfiguration;

    /**
     * AliasFilterManager constructor.
     */
    public function __construct(FilterManager $filterManager, FilterConfiguration $filterConfiguration)
    {
        $this->filterManager       = $filterManager;
        $this->filterConfiguration = $filterConfiguration;
    }

    /**
     * Applies $variationName filters on $image.
     *
     * Both variations configured in eZ (SiteAccess context) and LiipImagineBundle are used.
     * An eZ variation may have a "reference".
     * In that case, reference's filters are applied first, recursively (a reference may also have another reference).
     * Reference must be a valid variation name, configured in eZ or in LiipImagineBundle.
     *
     * @param string $variationName
     */
    public function applyFilter(
        BinaryInterface $image,
        $variationName,
        array $runtimeFiltersConfig = []
    ): BinaryInterface {
        $filterConfig = $this->filterConfiguration->get($variationName);
        // If the variation has a reference, we recursively call this method to apply reference's filters.
        if (
            isset($filterConfig['reference'])
            && IORepositoryResolver::VARIATION_ORIGINAL !== $filterConfig['reference']
        ) {
            $image = $this->applyFilter($image, $filterConfig['reference'], $runtimeFiltersConfig);
        }

        return $this->filterManager->applyFilter($image, $variationName, $runtimeFiltersConfig);
    }
}
