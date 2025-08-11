<?php

namespace AlmaviaCX\Ibexa\Commerce\Extra\Service\SiteAccessAware\Cart;

use AlmaviaCX\Ibexa\Commerce\Extra\Service\SiteAccessAware\Traits\ConfigResolverTrait;

class NamedCartService
{
    use ConfigResolverTrait;

    public const CUSTOM_CART_NAME_PARAMETER = 'named_cart_name';
    public const NAMED_CART_ENABLED_NAME_PARAMETER = 'named_cart_enabled';
    public const NAMED_CART_ENABLED_CONTEXT_PARAMETER = 'named_cart_context';
    public const DEFAULT_CART_NAME = 'default';
    /**
     * @param string $cartName
     * @return string
     */
    public function getNamedCartName(): string
    {
        $cartName = self::DEFAULT_CART_NAME;
        if ((bool) $this->getConfigParameter(self::NAMED_CART_ENABLED_NAME_PARAMETER) === true) {
            $cartName = (string) $this->getConfigParameter(self::CUSTOM_CART_NAME_PARAMETER);
        }
        return !empty($cartName) ? $cartName : self::DEFAULT_CART_NAME;
    }
}
