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

namespace Novactive\EzEnhancedImageAsset\Imagine\Filter;

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

    protected $defaultPostProcessors;

    protected $defaultConfig;

    /**
     * FilterConfiguration constructor.
     *
     * @param BaseFilterConfiguration $filterConfiguration
     */
    public function __construct(BaseFilterConfiguration $filterConfiguration)
    {
        $this->filterConfiguration = $filterConfiguration;
    }

    /**
     * @param mixed $defaultPostProcessors
     */
    public function setDefaultPostProcessors($defaultPostProcessors): void
    {
        $this->defaultPostProcessors = $defaultPostProcessors;
    }

    /**
     * @param mixed $defaultConfig
     */
    public function setDefaultConfig($defaultConfig): void
    {
        $this->defaultConfig = $defaultConfig;
    }

    /**
     * @param string $filter
     *
     * @return array
     */
    public function get($filter): array
    {
        $config = $this->filterConfiguration->get($filter);

        if (!isset($config['jpeg_quality'])) {
            $config['jpeg_quality'] = 70;
        }
        if (!isset($config['png_compression_level'])) {
            $config['png_compression_level'] = 6;
        }
        if ($this->defaultPostProcessors && (!isset($config['post_processors']) || empty($config['post_processors']))) {
            $config['post_processors'] = $this->defaultPostProcessors;
        }

        if ($this->defaultConfig) {
            $config += $this->defaultConfig;
        }

        return $config;
    }

    /**
     * Sets a configuration on the given filter.
     *
     * @param string $filter
     * @param array  $config
     */
    public function set($filter, array $config): void
    {
        $this->filterConfiguration->set($filter, $config);
    }

    /**
     * Get all filters.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->filterConfiguration->all();
    }
}
