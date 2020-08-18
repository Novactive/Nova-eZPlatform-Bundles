<?php

/**
 * NovaeZEditHelpBundle.
 *
 * @package   Novactive\Bundle\NovaeZEditHelpBundle
 *
 * @author    sergmike
 * @copyright 2019
 * @license   https://github.com/Novactive/NovaeZEditHelpBundle MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\NovaeZEditHelpBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

class NovaeZEditHelpExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $configFile = __DIR__.'/../Resources/config/design.yml';
        $config = Yaml::parse(file_get_contents($configFile));
        $container->prependExtensionConfig('ezdesign', $config);
        $container->addResource(new FileResource($configFile));
    }
}
