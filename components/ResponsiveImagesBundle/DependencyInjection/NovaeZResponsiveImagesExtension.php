<?php

/**
 * Nova eZ Responsive Images Bundle Extension.
 *
 * @package   Novactive\Bundle\eZResponsiveImagesBundle
 *
 * @author    Novactive <novaezresponsiveimages@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZResponsiveImagesBundle/blob/master/LICENSE MIT Licence
 */

namespace Novactive\Bundle\eZResponsiveImagesBundle\DependencyInjection;

use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

class NovaeZResponsiveImagesExtension extends Extension implements PrependExtensionInterface
{
    public function getAlias(): string
    {
        return 'novaez_responsive_images';
    }

    public function prepend(ContainerBuilder $container): void
    {
        $fieldOverrideFile = __DIR__.'/../Resources/config/ez_field_templates.yml';
        $config = Yaml::parse(file_get_contents($fieldOverrideFile));
        $container->prependExtensionConfig('ezpublish', $config);
        $container->addResource(new FileResource($fieldOverrideFile));
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);
    }
}
