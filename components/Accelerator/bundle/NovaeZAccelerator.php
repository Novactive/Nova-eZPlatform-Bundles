<?php

/**
 * Nova eZ Accelerator.
 *
 * @package   Novactive\Bundle\eZAccelerator
 *
 * @author    Novactive <dir.tech@novactive.com>
 * @author    Sébastien Morel (Plopix) <morel.seb@gmail.com>
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZAccelerator/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZAccelerator;

use Novactive\Bundle\eZAccelerator\DependencyInjection\Compiler\EventPass;
use Novactive\Bundle\eZAccelerator\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class NovaeZAccelerator extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new EventPass());
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $extension = new Extension();
            if (!$extension instanceof ExtensionInterface) {
                $fqdn = \get_class($extension);
                $message = 'Extension %s must implement %s.';
                throw new \LogicException(sprintf($message, $fqdn, ExtensionInterface::class));
            }
            $this->extension = $extension;
        }

        return $this->extension;
    }
}
