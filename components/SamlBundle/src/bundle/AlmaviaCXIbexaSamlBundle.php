<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaSamlBundle;

use AlmaviaCX\Bundle\IbexaSamlBundle\DependencyInjection\Compiler\LazySaml2Auth;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AlmaviaCXIbexaSamlBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new LazySaml2Auth());
    }
}
