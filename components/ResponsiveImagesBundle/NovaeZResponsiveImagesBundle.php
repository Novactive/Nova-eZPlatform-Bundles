<?php

/**
 * Nova eZ Responsive Images Bundle Configuration.
 *
 * @package   Novactive\Bundle\eZResponsiveImagesBundle
 *
 * @author    Novactive <novaezresponsiveimages@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZResponsiveImagesBundle/blob/master/LICENSE MIT Licence
 */

namespace Novactive\Bundle\eZResponsiveImagesBundle;

use LogicException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NovaeZResponsiveImagesBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $extension = $this->createContainerExtension();
            if (null !== $extension) {
                if (!$extension instanceof ExtensionInterface) {
                    throw new LogicException(
                        sprintf('Extension %s must implement '.ExtensionInterface::class.'.', \get_class($extension))
                    );
                }
                $this->extension = $extension;
            } else {
                $this->extension = false;
            }
        }
        if ($this->extension) {
            return $this->extension;
        }
    }
}
