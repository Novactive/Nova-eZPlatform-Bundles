<?php

namespace AlmaviaCX\Ibexa\Commerce\Extra\DependencyInjection;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Yaml;

class AlmaviaCXIbexaCommerceExtraExtension extends Extension implements PrependExtensionInterface
{
    public function getAlias(): string
    {
        return Configuration::CONFIGRESOLVER_NAMESPACE;
    }
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('default_config.yaml');

        $processor = new ConfigurationProcessor($container, Configuration::CONFIGRESOLVER_NAMESPACE);
        $processor->mapConfig(
            $config,
            static function ($scopeSettings, $currentScope, ContextualizerInterface $contextualizer) {
                if (array_key_exists('named_cart_enabled', $scopeSettings)) {
                    $contextualizer->setContextualParameter(
                        'named_cart_enabled',
                        $currentScope,
                        $scopeSettings['named_cart_enabled']
                    );
                }
                if (array_key_exists('named_cart_name', $scopeSettings)) {
                    $contextualizer->setContextualParameter(
                        'named_cart_name',
                        $currentScope,
                        $scopeSettings['named_cart_name']
                    );
                }
                if (array_key_exists('named_cart_enabled', $scopeSettings)) {
                    $contextualizer->setContextualParameter(
                        'named_cart_enabled',
                        $currentScope,
                        $scopeSettings['named_cart_enabled']
                    );
                }
                if (array_key_exists('named_cart_context', $scopeSettings)) {
                    $contextualizer->setContextualParameter(
                        'named_cart_context',
                        $currentScope,
                        $scopeSettings['named_cart_context']
                    );
                }
                if (array_key_exists('named_workflow_name', $scopeSettings)) {
                    $contextualizer->setContextualParameter(
                        'named_workflow_name',
                        $currentScope,
                        $scopeSettings['named_workflow_name']
                    );
                }
            }
        );
        $loader->load('services.yaml');
    }

    public function prepend(ContainerBuilder $container)
    {
    }
}
