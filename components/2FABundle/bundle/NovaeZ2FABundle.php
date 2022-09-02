<?php

/**
 * NovaeZ2FABundle.
 *
 * @package   NovaeZ2FABundle
 *
 * @author    Maxim Strukov <maxim.strukov@almaviacx.com>
 * @copyright 2021 AlmaviaCX
 * @license   https://github.com/Novactive/NovaeZ2FA/blob/main/LICENSE
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZ2FABundle;

use LogicException;
use Novactive\Bundle\eZ2FABundle\DependencyInjection\NovaeZ2FAExtension;
use Novactive\Bundle\eZ2FABundle\DependencyInjection\Security\PolicyProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class NovaeZ2FABundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $eZExtension = $container->getExtension('ibexa');
        $eZExtension->addPolicyProvider(new PolicyProvider());
    }

    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $extension = new NovaeZ2FAExtension();
            if (!$extension instanceof ExtensionInterface) {
                $fqdn = \get_class($extension);
                $message = 'Extension %s must implement %s.';
                throw new LogicException(sprintf($message, $fqdn, ExtensionInterface::class));
            }
            $this->extension = $extension;
        }

        return $this->extension;
    }
}
