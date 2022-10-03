<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtraBundle\DependencyInjection;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
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
    public function getAlias(): string
    {
        return 'ez_solr_search_extra';
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('default_settings.yml');

        $activatedBundles = array_keys($container->getParameter('kernel.bundles'));
        if (in_array('IbexaTaxonomyBundle', $activatedBundles, true)) {
            $loader->load('taxonomy_aggregator.yml');
        }

        $processor = new ConfigurationProcessor($container, 'nova_solr_extra');
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

        $fieldNameGeneratorMap = $container->getParameter('ibexa.search.common.field_name_generator.map');
        $fieldNameGeneratorMap['ez_mdate'] = 'mdt';
        $container->setParameter('ibexa.search.common.field_name_generator.map', $fieldNameGeneratorMap);
    }
}
