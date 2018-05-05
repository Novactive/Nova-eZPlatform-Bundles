<?php
/**
 * NovaeZMailingBundle Bundle.
 *
 * @package   Novactive\Bundle\eZMailingBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMailingBundle/blob/master/LICENSE MIT Licence
 */

namespace Novactive\Bundle\eZMailingBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

/**
 * Class NovaeZMailingExtension.
 */
class NovaeZMailingExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'nova_ezmailing';
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('ezadminui.yml');
        $loader->load('default_settings.yml');
        $loader->load('services.yml');
        $asseticBundles   = $container->getParameter('assetic.bundles');
        $asseticBundles[] = 'NovaeZMailingBundle';
        $container->setParameter('assetic.bundles', $asseticBundles);

        $processor = new ConfigurationProcessor($container, $this->getAlias());
        $processor->mapSetting('simple_mailer', $config);
        $processor->mapSetting('mailing_mailer', $config);
    }

    /**
     * Loads configuration.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container): void
    {
        $configFile = __DIR__.'/../Resources/config/views.yml';
        $config     = Yaml::parse(file_get_contents($configFile));
        $container->prependExtensionConfig('ezpublish', $config);
        $container->addResource(new FileResource($configFile));

        $loggerFile = __DIR__.'/../Resources/config/logger.yml';
        $config     = Yaml::parse(file_get_contents($loggerFile));
        $container->prependExtensionConfig('monolog', $config);
        $container->addResource(new FileResource($loggerFile));

        $workflowFile = __DIR__.'/../Resources/config/workflow.yml';
        $config     = Yaml::parse(file_get_contents($loggerFile));
        $container->prependExtensionConfig('monolog', $config);
        $container->addResource(new FileResource($loggerFile));
    }
}
