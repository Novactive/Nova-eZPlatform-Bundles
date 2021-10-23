<?php

/**
 * NovaeZ2FABundle.
 *
 * @package   NovaeZ2FABundle
 *
 * @author    Maxim Strukov <maxim.strukov@almaviacx.com>
 * @copyright 2021 AlmaviaCX
 * @license   https://github.com/Novactive/NovaeZ2FA/blob/main/LICENSE
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZ2FABundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class NovaeZ2FAExtension extends Extension
{
    public function getAlias(): string
    {
        return Configuration::NAMESPACE;
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
        $loader->load('default_settings.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $processor = new ConfigurationProcessor($container, Configuration::NAMESPACE);
        $processor->mapSetting('2fa_mobile_method', $config);
        $processor->mapSetting('2fa_email_method_enabled', $config);
        $processor->mapConfigArray('config', $config);
    }
}
