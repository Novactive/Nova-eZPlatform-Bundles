<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaSamlBundle\DependencyInjection\Compiler;

use AlmaviaCX\Bundle\IbexaSaml\Security\Saml\SamlAuthFactory;
use OneLogin\Saml2\Auth;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class LazySaml2Auth implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(Auth::class)) {
            return;
        }

        $serviceDefinition = $container->getDefinition(Auth::class);
        $serviceDefinition->setFactory(new Reference(SamlAuthFactory::class));
        $serviceDefinition->setLazy(true);
    }
}
