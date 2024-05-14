<?php

declare(strict_types=1);

namespace AlmaviaCX\Ibexa\Bundle\FieldTypes\DependencyInjection;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

final class AlmaviaCXFieldTypesExtension extends Extension implements PrependExtensionInterface
{
    public function getAlias(): string
    {
        return Configuration::CONFIG_RESOLVER_NAMESPACE;
    }
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('default_settings.yaml');
        $processor = new ConfigurationProcessor($container, Configuration::CONFIG_RESOLVER_NAMESPACE    );
        $processor->mapConfigArray('acx_selection', $config, ContextualizerInterface::MERGE_FROM_SECOND_LEVEL);


        $loader->load('services.yaml');



    }

    public function prepend(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/field_types_templates.yaml';
        $container->prependExtensionConfig('ibexa', Yaml::parse(file_get_contents($configFile)));
        $container->addResource(new FileResource($configFile));
    }
}