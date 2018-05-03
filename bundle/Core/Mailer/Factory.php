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
use Novactive\Bundle\eZMailingBundle\Core\Provider\Broadcast;
use Novactive\Bundle\eZMailingBundle\Core\Provider\MailingContent;
use Novactive\Bundle\eZMailingBundle\Core\Provider\MessageContent;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Factory.
 */
class Factory
{
    /**
     * @var ConfigResolverInterface
     */
    private $configResolver;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var MessageContent
     */
    private $messageContentProvider;

    /**
     * @var MailingContent
     */
    private $mailingContentProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Broadcast
     */
    private $broadcastProvider;

    /**
     * Factory constructor.
     *
     * @param ConfigResolverInterface $configResolver
     * @param ContainerInterface      $container
     * @param MessageContent          $messageContentProvider
     * @param MailingContent          $mailingContentProvider
     * @param LoggerInterface         $logger
     * @param Broadcast               $broadcastProvider
     */
    public function __construct(
        ConfigResolverInterface $configResolver,
        ContainerInterface $container,
        MessageContent $messageContentProvider,
        MailingContent $mailingContentProvider,
        LoggerInterface $logger,
        Broadcast $broadcastProvider
    ) {
        $this->configResolver         = $configResolver;
        $this->container              = $container;
        $this->messageContentProvider = $messageContentProvider;
        $this->mailingContentProvider = $mailingContentProvider;
        $this->logger                 = $logger;
        $this->broadcastProvider      = $broadcastProvider;
    }

    /**
     * @param string $mailerDef
     *
     * @return Mailer
     */
    public function get(string $mailerDef): Mailer
    {
        $mailer = $this->container->get((string) $this->configResolver->getParameter($mailerDef, 'nova_ezmailing'));
        /* @var \Swift_Mailer $mailer */
        if ('simple_mailer' === $mailerDef) {
            return (new Simple($this->messageContentProvider, $this->logger))->setMailer($mailer);
        }
        if ('mailing_mailer' === $mailerDef) {
            $mailing = new Mailing(
                $this->container->get(Simple::class),
                $this->mailingContentProvider,
                $this->logger,
                $this->broadcastProvider
            );

            return $mailing->setMailer($mailer);
        }

        throw new \RuntimeException('Mailers are not correctly defined.');
    }
}
