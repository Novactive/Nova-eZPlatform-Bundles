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

use Novactive\Bundle\eZMailingBundle\Core\DataHandler\Registration;
use Novactive\Bundle\eZMailingBundle\Core\DataHandler\Unregistration;
use Novactive\Bundle\eZMailingBundle\Core\Provider\MessageContent;
use Novactive\Bundle\eZMailingBundle\Entity\ConfirmationToken;
use Novactive\Bundle\eZMailingBundle\Entity\Mailing as MailingEntity;
use Psr\Log\LoggerInterface;
use Swift_Message;

class Simple extends Mailer
{
    /**
     * @var MessageContent
     */
    private $messageProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(MessageContent $messageProvider, LoggerInterface $logger)
    {
        $this->messageProvider = $messageProvider;
        $this->logger = $logger;
    }

    public function sendStartSendingMailingMessage(MailingEntity $mailing): void
    {
        $message = $this->messageProvider->getStartSendingMailing($mailing);
        $this->sendMessage($message);
    }

    public function sendStopSendingMailingMessage(MailingEntity $mailing): void
    {
        $message = $this->messageProvider->getStopSendingMailing($mailing);
        $this->sendMessage($message);
    }

    public function sendRegistrationConfirmation(Registration $registration, ConfirmationToken $token): void
    {
        $message = $this->messageProvider->getRegistrationConfirmation($registration, $token);
        $this->sendMessage($message);
    }

    public function sendUnregistrationConfirmation(Unregistration $unregistration, ConfirmationToken $token): void
    {
        $message = $this->messageProvider->getUnregistrationConfirmation($unregistration, $token);
        $this->sendMessage($message);
    }

    private function sendMessage(Swift_Message $message): int
    {
        $this->logger->debug("Simple Mailer sends {$message->getSubject()}.");

        return $this->mailer->send($message);
    }
}
