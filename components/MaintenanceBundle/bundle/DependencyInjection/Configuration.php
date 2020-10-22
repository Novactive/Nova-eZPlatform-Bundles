<?php

/**
 * NovaeZMaintenanceBundle.
 *
 * @package   Novactive\NovaeZMaintenanceBundle
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZMaintenanceBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\NovaeZMaintenanceBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\Configuration as SAConfiguration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration extends SAConfiguration
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('nova_ezmaintenance');
        $rootNode = $treeBuilder->getRootNode();
        $systemNode = $this->generateScopeBaseNode($rootNode);
        $systemNode
            ->scalarNode('enable')->end()
            ->scalarNode('template')->end()
            ->scalarNode('lock_file_id')->end();

        return $treeBuilder;
    }
}
