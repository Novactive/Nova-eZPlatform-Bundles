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

namespace Novactive\EzEnhancedImageAssetBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

/**
 * Class EzEnhancedImageAssetExtension.
 *
 * @package Novactive\EzEnhancedImageAssetBundle\DependencyInjection
 */
class EzEnhancedImageAssetExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     *
     * @throws Exception
     * @throws Exception
     * @throws Exception
     * @throws Exception
     * @throws Exception
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('default_settings.yml');
        $loader->load('fieldtypes.yml');
        $loader->load('field_value_converters.yml');
        $loader->load('migration.yml');
    }

    /**
     * Allow an extension to prepend the extension configurations.
     */
    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig(
            'assetic',
            [
                'bundles' => [
                    'EzEnhancedImageAssetBundle',
                ],
            ]
        );

        $configs = [
            'field_templates.yml' => 'ezpublish',
            'admin_ui_forms.yml' => 'ezpublish',
            'image_variations.yml' => 'ezpublish',
        ];

        foreach ($configs as $fileName => $extensionName) {
            $configFile = __DIR__.'/../Resources/config/'.$fileName;
            $config = Yaml::parse((string) file_get_contents($configFile));
            $container->prependExtensionConfig($extensionName, $config);
            $container->addResource(new FileResource($configFile));
        }

        $configs = $container->getExtensionConfig('ezpublish');
        $newConfig = [];
        foreach ($configs as $config) {
            if (!isset($config['system'])) {
                continue;
            }

            foreach ($config['system'] as $system => $systemConfig) {
                if (!isset($systemConfig['image_variations'])) {
                    continue;
                }

                foreach (array_keys($systemConfig['image_variations']) as $imageVariation) {
                    if (false !== strpos($imageVariation, '_retina')) {
                        $webpVariationName = preg_replace('/^(.+)(_retina)$/', '$1_webp$2', $imageVariation);
                    } else {
                        $webpVariationName = $imageVariation.'_webp';
                    }
                    $newConfig['system'][$system]['image_variations'][$webpVariationName] = [
                        'reference' => $imageVariation,
                        'filters' => [
                            ['name' => 'toFormat', 'params' => ['format' => 'webp']],
                        ],
                    ];
                }
            }
        }
        $container->prependExtensionConfig('ezpublish', $newConfig);
    }
}
