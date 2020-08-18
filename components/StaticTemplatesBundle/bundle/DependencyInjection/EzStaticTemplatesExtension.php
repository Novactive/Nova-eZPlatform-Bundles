<?php

/**
 * NovaeZStaticTemplatesBundle.
 *
 * @package   Novactive\Bundle\EzStaticTemplatesBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZStaticTemplatesBundle/blob/master/LICENSE
 */

namespace Novactive\Bundle\EzStaticTemplatesBundle\DependencyInjection;

use ReflectionClass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class EzStaticTemplatesExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * @inheritDoc
     */
    public function prepend(ContainerBuilder $container)
    {
        $siteaccessList = $this->getSiteaccessIdentifierList($container);
        if (!empty($siteaccessList)) {
            $ezpublishConfig = [
                'siteaccess' => [
                    'list' => [],
                    'groups' => [
                        'static_group' => [],
                    ],
                    'match' => [
                        'Map\URI' => [],
                    ],
                ],
                'system' => [],
            ];
            $ezdesignConfig = [
                'design_list' => [],
            ];
            foreach ($siteaccessList as $theme) {
                $uri = str_replace('_', '-', $theme);
                $ezpublishConfig['siteaccess']['list'][] = $theme;
                $ezpublishConfig['siteaccess']['groups']['static_group'][] = $theme;
                $ezpublishConfig['siteaccess']['match']['Map\URI'][$uri] = $theme;
                $ezpublishConfig['system'][$theme] = ['design' => "{$theme}_design"];
                $ezdesignConfig['design_list']["{$theme}_design"] = [$theme];
            }
            $container->prependExtensionConfig('ezpublish', $ezpublishConfig);
            $container->prependExtensionConfig('ezdesign', $ezdesignConfig);
        }
    }

    /**
     * @throws \ReflectionException
     */
    protected function getSiteaccessIdentifierList(ContainerBuilder $container): array
    {
        $StaticTemplatesThemePrefix = 'static_';
        $siteaccessList = [];

        $globalViewsDir = $container->getParameter('kernel.root_dir').'/Resources/views';
        if (!is_dir($globalViewsDir)) {
            ( new Filesystem() )->mkdir($globalViewsDir);
        }
        $finder = new Finder();
        // Look for themes in bundles.
        foreach ($container->getParameter('kernel.bundles') as $bundleClass) {
            $bundleReflection = new ReflectionClass($bundleClass);
            $bundleViewsDir = dirname($bundleReflection->getFileName()).'/Resources/views';
            $themeDir = $bundleViewsDir.'/themes';
            if (!is_dir($themeDir)) {
                continue;
            }

            /** @var \Symfony\Component\Finder\SplFileInfo $directoryInfo */
            foreach ($finder->directories()->in($themeDir)->depth('== 0') as $directoryInfo) {
                if (preg_match("/^{$StaticTemplatesThemePrefix}(.*)$/", $directoryInfo->getBasename())) {
                    $siteaccessList[] = $directoryInfo->getBasename();
                }
            }
        }

        // Now look for themes at application level (app/Resources/views/themes)
        $appLevelThemesDir = $globalViewsDir.'/themes';
        if (is_dir($appLevelThemesDir)) {
            foreach (( new Finder() )->directories()->in($appLevelThemesDir)->depth('== 0') as $directoryInfo) {
                if (preg_match("/^{$StaticTemplatesThemePrefix}(.*)$/", $directoryInfo->getBasename())) {
                    $siteaccessList[] = $directoryInfo->getBasename();
                }
            }
        }
        array_unique($siteaccessList);

        return $siteaccessList;
    }
}
