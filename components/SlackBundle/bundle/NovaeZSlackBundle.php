<?php

/**
 * NovaeZSlackBundle Bundle.
 *
 * @package   Novactive\Bundle\eZSlackBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZSlackBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZSlackBundle;

use LogicException;
use Novactive\Bundle\eZSlackBundle\DependencyInjection\CompilerPass\InteractionsPass;
use Novactive\Bundle\eZSlackBundle\DependencyInjection\CompilerPass\TranslatableJsonSerializationCompilerPass;
use Novactive\Bundle\eZSlackBundle\DependencyInjection\NovaeZSlackExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class NovaeZSlackBundle.
 */
class NovaeZSlackBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new TranslatableJsonSerializationCompilerPass());
        $container->addCompilerPass(new InteractionsPass());
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $extension = new NovaeZSlackExtension();
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
