<?php

namespace AlmaviaCX\Bundle\IbexaRichTextExtraBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

class AlmaviaCXIbexaRichTextExtraExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('default_settings.yaml');
        $loader->load('services.yaml');
    }

    public function prepend(ContainerBuilder $container)
    {
        $coreExtensionConfigFile = realpath(__DIR__.'/../Resources/config/prepend/ibexa.yaml');
        $container->prependExtensionConfig('ibexa', Yaml::parseFile($coreExtensionConfigFile));
        $container->addResource(new FileResource($coreExtensionConfigFile));
    }
}
