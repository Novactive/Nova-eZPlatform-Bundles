<?php

/**
 * NovaeZMaintenanceBundle.
 *
 * @package   NovaeZMaintenanceBundle
 *
 * @author    Julien RONDIN
 * @copyright 2021 AlmaviaCX
 * @license   https://github.com/Novactive/NovaeZMaintenanceBundle/blob/master/LICENSE
 */

namespace Novactive\NovaeZMaintenanceBundle\DependencyInjection\Security;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Security\PolicyProvider\YamlPolicyProvider;

class MaintenancePolicyProvider extends YamlPolicyProvider
{
    public function getFiles(): array
    {
        return [
            __DIR__.'/../../Resources/config/policies.yml',
        ];
    }
}
