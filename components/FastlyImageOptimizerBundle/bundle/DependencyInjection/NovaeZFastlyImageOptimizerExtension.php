<?php

/**
 * Novactive eZ Fastly Image Optimizer Bundle.
 *
 * @author    Novactive <direction.technique@novactive.com>
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZFastlyImageOptimizerBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZFastlyImageOptimizerBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

class NovaeZFastlyImageOptimizerExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
        $loader->load('default_settings.yaml');
        $processor = new ConfigurationProcessor($container, 'nova_ezfastlyio');
        $processor->mapConfigArray('fastlyio_variations', $config);
        $processor->mapSetting('fastlyio_disabled', $config);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $fieldOverrideFile = __DIR__.'/../Resources/config/field_override.yaml';
        $config = Yaml::parse(file_get_contents($fieldOverrideFile));
        $container->prependExtensionConfig('ezplatform', $config);
        $container->addResource(new FileResource($fieldOverrideFile));
    }

    public function getAlias(): string
    {
        return 'nova_ezfastlyio';
    }
}
