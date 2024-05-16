<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtraBundle\DependencyInjection\Security\PolicyProvider;

use Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\YamlPolicyProvider;

class EZSolrSearchPolicyProvider extends YamlPolicyProvider
{
    /**
     * EZSolrSearchPolicyProvider constructor.
     */
    public function __construct(protected string $path)
    {
    }

    public function getFiles(): array
    {
        return [$this->path.'/Resources/config/policies.yml'];
    }
}
