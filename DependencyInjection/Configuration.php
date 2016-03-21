<?php
/**
 * Nova eZ Responsive Images Bundle Configuration
 *
 * @package   Novactive\Bundle\eZResponsiveImagesBundle
 * @author    Novactive <novaezresponsiveimages@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZResponsiveImagesBundle/blob/master/LICENSE MIT Licence
 */
namespace Novactive\Bundle\eZResponsiveImagesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('novae_zresponsive_images');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
