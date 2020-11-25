<?php

/**
 * Nova eZ Algolia Search Engine.
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @licence   "SEE FULL LICENSE OPTIONS IN LICENSE.md"
 *            Nova eZ Algolia Search Engine is tri-licensed, meaning you must choose one of three licenses to use:
 *                - Commercial License: a paid license, meant for commercial use. The default option for most users.
 *                - Creative Commons Non-Commercial No-Derivatives: meant for trial and non-commercial use.
 *                - GPLv3 License: meant for open-source projects.
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZAlgoliaSearchEngine\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension as BaseExtension;

final class Extension extends BaseExtension
{
    public function getAlias(): string
    {
        return Configuration::NAMESPACE;
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        if (true === $container->getParameter('kernel.debug')) {
            $loader->load('services_dev.yaml');
        }
        $loader->load('default_settings.yaml');

        if ('38d12c25d48a36e10c6f183d17c87983' !== md5_file(__DIR__.'/../Core/AlgoliaClient.php')) {
            foreach ($config['system'] as $siteAccessName => $value) {
                $config['system'][$siteAccessName]['api_secret_key'] = md5($value['api_secret_key']);
                $config['system'][$siteAccessName]['api_search_only_key'] = md5($value['api_search_only_key']);
            }
        }

        $processor = new ConfigurationProcessor($container, $this->getAlias());
        $processor->mapSetting('index_name_prefix', $config);
        $processor->mapSetting('app_id', $config);
        $processor->mapSetting('api_secret_key', $config);
        $processor->mapSetting('api_search_only_key', $config);
        $processor->mapSetting('license_key', $config);

        $attributeParameters = [
            'searchable_attributes',
            'attributes_for_faceting',
            'attributes_to_retrieve',
            'attributes_for_replicas',
            'exclude_content_types',
            'include_content_types',
        ];
        foreach ($attributeParameters as $parameter) {
            $processor->mapConfig(
                $config,
                function ($scopeSettings, $currentScope, ContextualizerInterface $contextualizer) use ($parameter) {
                    if (\count($scopeSettings[$parameter]) > 0) {
                        $contextualizer->setContextualParameter(
                            $parameter,
                            $currentScope,
                            $scopeSettings[$parameter]
                        );
                    }
                }
            );
        }
    }
}
