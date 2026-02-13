<?php

declare(strict_types=1);

namespace Novactive\Bundle\IbexaRecaptchaFieldBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Yaml\Yaml;

final class AlmaviaCXIbexaRecaptchaFieldExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $config = Yaml::parseFile(
            __DIR__ . '/../Resources/config/config.yml'
        );

        foreach ($config as $extension => $settings) {
            $container->prependExtensionConfig($extension, $settings);
        }
    }
}
