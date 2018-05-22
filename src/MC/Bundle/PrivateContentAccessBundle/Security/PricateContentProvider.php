<?php

namespace MC\Bundle\PrivateContentAccessBundle\Security;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Security\PolicyProvider\YamlPolicyProvider;

class PricateContentProvider extends YamlPolicyProvider
{
    protected function getFiles()
    {
        return [
        __DIR__ . '/../Resources/config/policies.yml',
        ];
    }
}