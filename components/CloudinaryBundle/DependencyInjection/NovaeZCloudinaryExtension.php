<?php

/**
 * Novactive eZ Cloudinary Bundle.
 *
 * @package   Novactive\Bundle\eZCloudinary
 *
 * @author    Novactive <novacloudinarybundle@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZCloudinaryBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZCloudinaryBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

class NovaeZCloudinaryExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('nova_ezcloudinary.authentification', $config['authentification']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('default_settings.yml');

        $processor = new ConfigurationProcessor($container, 'nova_ezcloudinary');

        $processor->mapSetting('cloudinary_disabled', $config);
        $processor->mapSetting('cloudinary_fallback_variation', $config);
        $processor->mapConfigArray('cloudinary_variations', $config);
        $processor->mapConfigArray('cloudinary_fecth_proxy', $config);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $fieldOverrideFile = __DIR__.'/../Resources/config/field_override.yml';
        $config = Yaml::parse(file_get_contents($fieldOverrideFile));
        $container->prependExtensionConfig('ezplatform', $config);
        $container->addResource(new FileResource($fieldOverrideFile));
    }

    public function getAlias(): string
    {
        return 'nova_ezcloudinary';
    }
}
