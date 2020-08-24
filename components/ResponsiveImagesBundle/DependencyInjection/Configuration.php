<?php

/**
 * Nova eZ Responsive Images Bundle Configuration.
 *
 * @package   Novactive\Bundle\eZResponsiveImagesBundle
 *
 * @author    Novactive <novaezresponsiveimages@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZResponsiveImagesBundle/blob/master/LICENSE MIT Licence
 */

namespace Novactive\Bundle\eZResponsiveImagesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        return new TreeBuilder('novaez_responsive_images');
    }
}
