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

use Novactive\Bundle\eZMailingBundle\Core\Provider\MessageContent;
use Novactive\Bundle\eZMailingBundle\Entity\Mailing as MailingEntity;
use Swift_Message;

/**
 * Class Simple.
 */
class Simple extends Mailer
{
    /**
     * @var MessageContent
     */
    private $messageProvider;

    /**
     * Simple constructor.
     *
     * @param MessageContent $messageProvider
     */
    public function __construct(MessageContent $messageProvider)
    {
        $this->messageProvider = $messageProvider;
    }

    /**
     * @param MailingEntity $mailing
     */
    public function sendStartSendingMailingMessage(MailingEntity $mailing): void
    {
        $message = $this->messageProvider->getStartSendingMailing($mailing);
        $this->sendMessage($message);
    }

    /**
     * @param MailingEntity $mailing
     */
    public function sendStopSendingMailingMessage(MailingEntity $mailing): void
    {
        $message = $this->messageProvider->getStopSendingMailing($mailing);
        $this->sendMessage($message);
    }

    /**
     * @param Swift_Message $message
     *
     * @return int
     */
    private function sendMessage(Swift_Message $message): int
    {
        return $this->mailer->send($message);
    }
}
