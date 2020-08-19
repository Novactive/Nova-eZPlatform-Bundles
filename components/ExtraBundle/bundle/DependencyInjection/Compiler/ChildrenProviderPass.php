<?php

/**
 * NovaeZExtraBundle ChildrenProviderPass.
 *
 * @package   Novactive\Bundle\eZExtraBundle
 *
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZExtraBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ChildrenProviderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('novactive.ezextra.pre_content_view_listener')) {
            return;
        }

        $definition = $container->findDefinition(
            'novactive.ezextra.pre_content_view_listener'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'novactive.ezextra.children.provider'
        );
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall(
                    'addManagedType',
                    [new Reference($id), $attributes['contentTypeIdentifier']]
                );
            }
        }
    }
}
