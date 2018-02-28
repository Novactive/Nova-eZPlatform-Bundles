<?php
/**
 * NovaeZMailingBundle Bundle.
 *
 * @package   Novactive\Bundle\eZMailingBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMailingBundle/blob/master/LICENSE MIT Licence
 */
declare(strict_types=1);

namespace Novactive\Bundle\eZMailingBundle\Core\Mailer;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Psr\Container\ContainerInterface;

/**
 * Class Factory
 */
class Factory
{
    /**
     * @var ConfigResolverInterface
     */
    private $configResolver;

    /** @var ContainerInterface */

    private $container;

    /**
     * Factory constructor.
     *
     * @param ConfigResolverInterface $configResolver
     * @param ContainerInterface      $container¬
     */
    public function __construct(ConfigResolverInterface $configResolver, ContainerInterface $container)
    {
        $this->configResolver = $configResolver;
        $this->container      = $container;
    }

    /**
     * @param string $mailerDef
     *
     * @return Mailer
     */
    public function get(string $mailerDef): Mailer
    {
        $mailer = $this->container->get((string) $this->configResolver->getParameter($mailerDef, 'nova_ezmailing'));
        /** @var \Swift_Mailer $mailer */
        if ($mailerDef === 'simple_mailer') {
            return (new Simple())->setMailer($mailer);
        }
        if ($mailerDef === 'mailing_mailer') {
            return (new Mailing($this->container->get(Simple::class)))->setMailer($mailer);
        }

        throw new \RuntimeException("Mailers are not correctly defined.");
    }
}
