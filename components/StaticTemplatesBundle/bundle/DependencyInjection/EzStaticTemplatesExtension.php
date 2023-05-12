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

declare(strict_types=1);

namespace Novactive\Bundle\EzStaticTemplatesBundle\DependencyInjection;

use ReflectionClass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class EzStaticTemplatesExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
    }

    public function prepend(ContainerBuilder $container): void
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
            $container->prependExtensionConfig('ibexa', $ezpublishConfig);
            $container->prependExtensionConfig('ibexa_design_engine', $ezdesignConfig);
        }
    }

    protected function getSiteaccessIdentifierList(ContainerBuilder $container): array
    {
        $StaticTemplatesThemePrefix = 'static_';
        $siteaccessList = [];

        $fs = new Filesystem();
        $finder = new Finder();

        // Look for themes in bundles.
        foreach ($container->getParameter('kernel.bundles') as $bundleClass) {
            $bundleReflection = new ReflectionClass($bundleClass);
            $bundleViewsDir = \dirname($bundleReflection->getFileName()).'/Resources/views';
            $themeDir = $bundleViewsDir.'/themes';
            if (!$fs->exists($themeDir)) {
                continue;
            }

            /** @var SplFileInfo $directoryInfo */
            foreach ($finder->directories()->in($themeDir)->depth('== 0') as $directoryInfo) {
                if (preg_match("/^{$StaticTemplatesThemePrefix}(.*)$/", $directoryInfo->getBasename())) {
                    $siteaccessList[] = $directoryInfo->getBasename();
                }
            }
        }

        // Now look for themes at application level (app/Resources/views/themes)
        $appLevelThemesDir = $container->getParameter('kernel.project_dir').'/templates/themes';
        if ($fs->exists($appLevelThemesDir)) {
            foreach ((new Finder())->directories()->in($appLevelThemesDir)->depth('== 0') as $directoryInfo) {
                if (preg_match("/^{$StaticTemplatesThemePrefix}(.*)$/", $directoryInfo->getBasename())) {
                    $siteaccessList[] = $directoryInfo->getBasename();
                }
            }
        }
        array_unique($siteaccessList);

        return $siteaccessList;
    }
}
