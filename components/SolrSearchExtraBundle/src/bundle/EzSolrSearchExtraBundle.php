<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtraBundle;

use Novactive\EzSolrSearchExtraBundle\DependencyInjection\Security\PolicyProvider\EZSolrSearchPolicyProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class EzSolrSearchExtraBundle.
 *
 * @package Novactive\EzSolrSearchExtraBundle
 */
class EzSolrSearchExtraBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $ibexaCoreExtension */
        $ibexaCoreExtension = $container->getExtension('ibexa');
        $ibexaCoreExtension->addPolicyProvider(new EZSolrSearchPolicyProvider($this->getPath()));
    }
}
