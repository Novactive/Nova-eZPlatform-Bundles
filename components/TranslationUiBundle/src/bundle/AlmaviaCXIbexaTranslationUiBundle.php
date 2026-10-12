<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaTranslationUiBundle;

use AlmaviaCX\Bundle\IbexaTranslationUiBundle\DependencyInjection\Security\TranslationPolicyProvider;
use Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AlmaviaCXIbexaTranslationUiBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        /** @var IbexaCoreExtension $ibexaExtension */
        $ibexaExtension = $container->getExtension('ibexa');

        $ibexaExtension->addPolicyProvider(
            new TranslationPolicyProvider()
        );
    }
}
