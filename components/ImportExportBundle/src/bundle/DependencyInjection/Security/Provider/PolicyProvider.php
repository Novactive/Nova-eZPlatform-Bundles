<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExportBundle\DependencyInjection\Security\Provider;

use Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\YamlPolicyProvider;

class PolicyProvider extends YamlPolicyProvider
{
    /**
     * @return string[]
     */
    public function getFiles(): array
    {
        return [
            __DIR__.'/../../../Resources/config/policies.yaml',
        ];
    }
}
