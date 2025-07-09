<?php

namespace AlmaviaCX\Ibexa\Commerce\Extra\Service\SiteAccessAware\Traits;

use AlmaviaCX\Ibexa\Commerce\Extra\DependencyInjection\Configuration;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;

trait ConfigResolverTrait
{

    protected ConfigResolverInterface $configResolver;

    /**
     * @required
     * @param ConfigResolverInterface $configResolver
     * @return void
     */
    public function setConfigResolver(ConfigResolverInterface $configResolver): void
    {
        $this->configResolver = $configResolver;
    }

    /**
     * @param string $paramName
     * @param string|null $namespace
     * @param string|null $scope
     * @return mixed|null
     */
    public function getConfigParameter(string $paramName, ?string $namespace = null, ?string $scope = null)
    {
        $namespace ??= Configuration::CONFIGRESOLVER_NAMESPACE;
        return $this->configResolver->hasParameter($paramName, $namespace, $scope)?
            $this->configResolver->getParameter($paramName, $namespace, $scope): null;
    }
}