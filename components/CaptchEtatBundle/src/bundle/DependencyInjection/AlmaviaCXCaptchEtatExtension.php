<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\CaptchEtatBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class AlmaviaCXCaptchEtatExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('default_settings.yaml');
        $loader->load('services.yaml');
    }

    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('monolog', [
            'channels' => ['captcha_etat'],
        ]);
        $container->prependExtensionConfig('twig', [
            'form_themes' => ['captchetat-fields.html.twig'],
            'paths' => [__DIR__.'/../Resources/templates'],
        ]);
    }
}
