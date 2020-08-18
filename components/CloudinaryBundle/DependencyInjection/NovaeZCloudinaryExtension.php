<?php
/**
 * Novactive eZ Cloudinary Bundle
 *
 * @package   Novactive\Bundle\eZCloudinary
 * @author    Novactive <novacloudinarybundle@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZCloudinaryBundle/blob/master/LICENSE MIT Licence
 */

namespace Novactive\Bundle\eZCloudinaryBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Resource\FileResource;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;

/**
 * Class NovaeZCloudinaryExtension
 */
class NovaeZCloudinaryExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $container->setParameter("nova_ezcloudinary.authentification", $config['authentification']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('default_settings.yml');

        $processor = new ConfigurationProcessor($container, 'nova_ezcloudinary');
        $processor->mapConfigArray('cloudinary_variations', $config);
        $processor->mapConfigArray('cloudinary_fecth_proxy', $config);

    }

    /**
     * Loads configuration.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $fieldOverrideFile = __DIR__.'/../Resources/config/field_override.yml';
        $config            = Yaml::parse(file_get_contents($fieldOverrideFile));
        $container->prependExtensionConfig('ezpublish', $config);
        $container->addResource(new FileResource($fieldOverrideFile));
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return "nova_ezcloudinary";
    }
}
