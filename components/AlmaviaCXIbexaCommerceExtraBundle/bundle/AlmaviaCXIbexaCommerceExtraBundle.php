<?php

namespace  AlmaviaCX\Ibexa\Commerce\Extra;

use AlmaviaCX\Ibexa\Commerce\Extra\DependencyInjection\AlmaviaCXIbexaCommerceExtraExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AlmaviaCXIbexaCommerceExtraBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new AlmaviaCXIbexaCommerceExtraExtension();
    }
}
