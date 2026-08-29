<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExportBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

class AlmaviaCXIbexaImportExportExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @param mixed[] $configs
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('default_settings.yaml');
        $loader->load('controller.yaml');
        $loader->load('forms.yaml');
        $loader->load('event_subscriber.yaml');
        $loader->load('menu.yaml');
        $loader->load('services.yaml');
        $loader->load('accessor/ibexa/content_field_value_transformer.yaml');
        $loader->load('accessor/ibexa/object_accessor.yaml');
        $loader->load('item_value_transformer/transformers.yaml');
        $loader->load('workflow/component.yaml');
        $loader->load('workflow/job.yaml');
        $loader->load('workflow/execution.yaml');
        $loader->load('workflow/workflow.yaml');
        $loader->load('rest.yaml');

        $activatedBundles = array_keys($container->getParameter('kernel.bundles'));
        if (interface_exists('Symfony\Component\Messenger\MessageBusInterface')) {
            $loader->load('messenger.yaml');
        }
        if (in_array('IbexaTaxonomyBundle', $activatedBundles, true)) {
            $loader->load('item_value_transformer/taxonomy.yaml');
            $loader->load('taxonomy_services.yaml');
        }
//        if (in_array('IbexaFormBuilderBundle', $activatedBundles, true)) {
//        }
//        if (in_array('IbexaFieldTypePageBundle', $activatedBundles, true)) {
//        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        $ibexaOrmConfig = [
            'orm' => [
                'entity_mappings' => [
                    'AlmaviaCXIbexaImportExport' => [
                        'type' => 'annotation',
                        'dir' => __DIR__.'/../../lib',
                        'prefix' => 'AlmaviaCX\Bundle\IbexaImportExport',
                        'is_bundle' => false,
                    ],
                ],
            ],
        ];
        $container->prependExtensionConfig('ibexa', $ibexaOrmConfig);

        $configs = [
            'ibexa.yaml' => 'ibexa',
        ];

        foreach ($configs as $fileName => $extensionName) {
            $configFile = __DIR__.'/../Resources/config/prepend/'.$fileName;
            $config = Yaml::parse(file_get_contents($configFile));
            $container->prependExtensionConfig($extensionName, $config);
            $container->addResource(new FileResource($configFile));
        }
    }
}
