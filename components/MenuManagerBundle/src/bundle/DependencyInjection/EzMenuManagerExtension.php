<?php
/**
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 */

namespace Novactive\EzMenuManagerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

class EzMenuManagerExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('default_settings.yml');
        $loader->load('services.yml');
        $loader->load('fieldtypes.yml');
        $loader->load('indexable_fieldtypes.yml');
        $loader->load('field_value_converters.yml');
        $loader->load('migration.yml');
        $loader->load('ezadminui/components.yml');
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig(
            'assetic',
            [
                'bundles' => [
                    'EzMenuManagerBundle',
                ],
            ]
        );
        $container->prependExtensionConfig(
            'bazinga_js_translation',
            [
                'active_domains' => [
                    'menu_manager',
                ],
            ]
        );

        $configs = [
            'field_templates.yml'    => 'ezpublish',
            'field_templates_ui.yml' => 'ezpublish',
        ];

        $activatedBundles = array_keys($container->getParameter('kernel.bundles'));

        if (in_array('EzPlatformAdminUiBundle', $activatedBundles, true)) {
            $configs['ezadminui/twig.yml'] = 'twig';
        }

        foreach ($configs as $fileName => $extensionName) {
            $configFile = __DIR__.'/../Resources/config/'.$fileName;
            $config     = Yaml::parse(file_get_contents($configFile));
            $container->prependExtensionConfig($extensionName, $config);
            $container->addResource(new FileResource($configFile));
        }
    }
}
