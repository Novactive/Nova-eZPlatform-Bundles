<?php

/**
 * NovaeZSolrSearchExtraBundle.
 *
 * @package   NovaeZSolrSearchExtraBundle
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZSolrSearchExtraBundle/blob/master/LICENSE
 */

namespace Novactive\EzSolrSearchExtraBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class EzSolrSearchExtraExtension.
 *
 * @package Novactive\EzSolrSearchExtraBundle\DependencyInjection
 */
class EzSolrSearchExtraExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'ez_solr_search_extra';
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('default_settings.yml');

        $processor      = new ConfigurationProcessor($container, 'nova_solr_extra');
        $contextualizer = $processor->getContextualizer();
        $contextualizer->mapConfigArray(
            'fulltext_fields',
            $config,
            ContextualizerInterface::MERGE_FROM_SECOND_LEVEL
        );
        $contextualizer->mapConfigArray(
            'custom_fields',
            $config,
            ContextualizerInterface::MERGE_FROM_SECOND_LEVEL
        );
        $contextualizer->mapConfigArray(
            'publishdate_fields',
            $config,
            ContextualizerInterface::MERGE_FROM_SECOND_LEVEL
        );
    }
}
