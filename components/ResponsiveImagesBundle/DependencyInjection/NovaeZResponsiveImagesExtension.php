<?php
/**
 * Nova eZ Responsive Images Bundle Extension
 *
 * @package   Novactive\Bundle\eZResponsiveImagesBundle
 * @author    Novactive <novaezresponsiveimages@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZResponsiveImagesBundle/blob/master/LICENSE MIT Licence
 */
namespace Novactive\Bundle\eZResponsiveImagesBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class NovaeZResponsiveImagesExtension extends Extension implements PrependExtensionInterface
{

    /**
     * Add configuration
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $config = Yaml::parse(__DIR__.'/../Resources/config/ez_field_templates.yml');
        $container->prependExtensionConfig('ezpublish', $config);

        $config = Yaml::parse(__DIR__.'/../Resources/config/assetic.yml');
        $container->prependExtensionConfig('assetic', $config);
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $asseticBundles   = $container->getParameter('assetic.bundles');
        $asseticBundles[] = 'NovaeZResponsiveImagesBundle';
        $container->setParameter('assetic.bundles', $asseticBundles);

    }
}
