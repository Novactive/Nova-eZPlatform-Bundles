<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExportBundle;

use AlmaviaCX\Bundle\IbexaImportExportBundle\DependencyInjection\CompilerPass\ComponentPass;
use AlmaviaCX\Bundle\IbexaImportExportBundle\DependencyInjection\CompilerPass\ItemValueTransformerPass;
use AlmaviaCX\Bundle\IbexaImportExportBundle\DependencyInjection\CompilerPass\WorkflowPass;
use AlmaviaCX\Bundle\IbexaImportExportBundle\DependencyInjection\Security\Provider\PolicyProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AlmaviaCXIbexaImportExportBundle extends Bundle
{
    /**
     * Builds the bundle.
     *
     * It is only ever called once when the cache is empty.
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $ibexaExtension */
        $ibexaExtension = $container->getExtension('ibexa');
        $ibexaExtension->addPolicyProvider(new PolicyProvider());

        $container->addCompilerPass(new ComponentPass());
        $container->addCompilerPass(new WorkflowPass());
        $container->addCompilerPass(new ItemValueTransformerPass());
    }
}
