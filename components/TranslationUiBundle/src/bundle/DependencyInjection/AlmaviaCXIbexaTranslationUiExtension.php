<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaTranslationUiBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

class AlmaviaCXIbexaTranslationUiExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $configFile = __DIR__.'/../Resources/config/lexik_translation.yml';
        $config = Yaml::parse(file_get_contents($configFile));
        $container->prependExtensionConfig('lexik_translation', $config);
        $container->addResource(new FileResource($configFile));
    }
}
