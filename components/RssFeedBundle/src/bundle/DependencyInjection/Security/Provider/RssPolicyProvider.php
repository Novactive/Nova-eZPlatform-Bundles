<?php
/**
 * NovaeZRssFeedBundle.
 *
 * @package   NovaeZRssFeedBundle
 *
 * @author    Novactive
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZRssFeedBundle/blob/master/LICENSE
 */

namespace Novactive\EzRssFeedBundle\DependencyInjection\Security\Provider;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Security\PolicyProvider\YamlPolicyProvider;

class RssPolicyProvider extends YamlPolicyProvider
{
    /**
     * @return array
     */
    public function getFiles()
    {
        return [
            __DIR__ . '/../../../Resources/config/policies.yml',
        ];
    }
}
