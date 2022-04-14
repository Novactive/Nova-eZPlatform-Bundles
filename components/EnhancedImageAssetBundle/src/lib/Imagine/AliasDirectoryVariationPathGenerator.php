<?php

/*
 * Nova-eZPlatform-Bundles.
 *
 * @package   Nova-eZPlatform-Bundles
 *
 * @author    florian
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaHtmlIntegrationBundle/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Novactive\EzEnhancedImageAsset\Imagine;

use eZ\Bundle\EzPublishCoreBundle\Imagine\VariationPathGenerator;
use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration;

class AliasDirectoryVariationPathGenerator implements VariationPathGenerator
{
    /**
     * @var FilterConfiguration
     */
    protected $filterConfiguration;

    public function __construct(FilterConfiguration $filterConfiguration)
    {
        $this->filterConfiguration = $filterConfiguration;
    }

    public function getVariationPath($originalPath, $filter)
    {
        $filterConfig = $this->filterConfiguration->get($filter);
        $info = pathinfo($originalPath);

        $variationExtension = $filterConfig['format'] ?? $info['extension'];

        return sprintf(
            '_aliases/%s/%s/%s%s',
            $filter,
            $info['dirname'],
            $info['filename'],
            empty($variationExtension) ? '' : '.'.$variationExtension
        );
    }
}
