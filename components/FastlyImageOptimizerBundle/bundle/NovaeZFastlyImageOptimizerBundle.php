<?php

/**
 * Novactive eZ Fastly Image Optimizer Bundle.
 *
 * @author    Novactive <direction.technique@novactive.com>
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZFastlyImageOptimizerBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZFastlyImageOptimizerBundle;

use LogicException;
use Novactive\Bundle\eZFastlyImageOptimizerBundle\DependencyInjection\NovaeZFastlyImageOptimizerExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NovaeZFastlyImageOptimizerBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $extension = new NovaeZFastlyImageOptimizerExtension();
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
