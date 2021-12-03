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

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration as BaseFilterConfiguration;

/**
 * Class FilterConfiguration.
 *
 * @package Novactive\EzEnhancedImageAsset\Imagine\Filter
 */
class FilterConfiguration extends BaseFilterConfiguration
{
    /** @var BaseFilterConfiguration */
    protected $filterConfiguration;

    /** @var ConfigResolverInterface */
    protected $configResolver;

    /**
     * FilterConfiguration constructor.
     */
    public function __construct(BaseFilterConfiguration $filterConfiguration, ConfigResolverInterface $configResolver)
    {
        $this->filterConfiguration = $filterConfiguration;
        $this->configResolver = $configResolver;
        parent::__construct();
    }

    /**
     * @param string $filter
     */
    public function get($filter): array
    {
        $defaultPostProcessors = $this->getDefaultPostProcessors();
        $defaultConfig = $this->getDefaultConfig();
        $config = $this->filterConfiguration->get($filter);

        if (!isset($config['jpeg_quality'])) {
            $config['jpeg_quality'] = 70;
        }
        if (!isset($config['png_compression_level'])) {
            $config['png_compression_level'] = 6;
        }
        if ($defaultPostProcessors && (!isset($config['post_processors']) || empty($config['post_processors']))) {
            $config['post_processors'] = $defaultPostProcessors;
        }

        if ($defaultConfig) {
            $config += $defaultConfig;
        }

        return $config;
    }

    public function getDefaultPostProcessors(): ?array
    {
        return $this->configResolver->getParameter(
            'image_default_post_processors',
            'ez_enhanced_image_asset'
        );
    }

    public function getDefaultConfig(): ?array
    {
        return $this->configResolver->getParameter(
            'image_default_config',
            'ez_enhanced_image_asset'
        );
    }

    /**
     * Sets a configuration on the given filter.
     *
     * @param string $filter
     */
    public function set($filter, array $config): void
    {
        $this->filterConfiguration->set($filter, $config);
    }

    /**
     * Get all filters.
     */
    public function all(): array
    {
        return $this->filterConfiguration->all();
    }
}
