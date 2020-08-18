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
        $loader->load('ezadminui/components.yml');
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
            'field_templates.yml'   => 'ezpublish',
            'admin_ui_forms.yml'    => 'ezpublish',
            'image_variations.yml'  => 'ezpublish',
            'twig.yml'              => 'twig',
        ];

        $activatedBundles = array_keys($container->getParameter('kernel.bundles'));

        if (in_array('EzPlatformAdminUiBundle', $activatedBundles, true)) {
            $configs['ezadminui/twig.yml'] = 'twig';
        }

        foreach ($configs as $fileName => $extensionName) {
            $configFile = __DIR__.'/../Resources/config/'.$fileName;
            $config     = Yaml::parse((string) file_get_contents($configFile));
            $container->prependExtensionConfig($extensionName, $config);
            $container->addResource(new FileResource($configFile));
        }
    }
}
