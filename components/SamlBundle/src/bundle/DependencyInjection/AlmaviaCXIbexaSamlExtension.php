<?php

declare( strict_types=1 );

namespace AlmaviaCX\Bundle\IbexaSamlBundle\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

class AlmaviaCXIbexaSamlExtension extends Extension implements PrependExtensionInterface
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('default_settings.yaml');
        $loader->load('services.yaml');
    }

    public function prepend( ContainerBuilder $container )
    {
        $files = [
            'monolog' => 'monolog.yaml',
            'hslavich_onelogin_saml' => 'hslavich_onelogin_saml.yaml',
        ];
        foreach ( $files as $extensionName => $file )
        {
            $configFilePath = realpath(__DIR__.'/../Resources/config/prepend/'.$file);
            $container->prependExtensionConfig($extensionName, Yaml::parseFile($configFilePath));
            $container->addResource(new FileResource($configFilePath));
        }
    }
}
