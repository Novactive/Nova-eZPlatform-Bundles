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

use Novactive\Bundle\eZMailingBundle\Core\Provider\MailingContent;
use Novactive\Bundle\eZMailingBundle\Entity\Mailing as MailingEntity;
use Novactive\Bundle\eZMailingBundle\Entity\User;
use Psr\Log\LoggerInterface;
use Swift_Message;

/**
 * Class Mailing.
 */
class Mailing extends Mailer
{
    /**
     * @var Simple
     */
    private $simpleMailer;

    /**
     * @var MailingContent
     */
    private $contentProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Mailing constructor.
     *
     * @param Simple          $simpleMailer
     * @param MailingContent  $contentProvider
     * @param LoggerInterface $logger
     */
    public function __construct(Simple $simpleMailer, MailingContent $contentProvider, LoggerInterface $logger)
    {
        $this->simpleMailer    = $simpleMailer;
        $this->contentProvider = $contentProvider;
        $this->logger          = $logger;
    }

    /**
     * @param MailingEntity $mailing
     */
    public function sendMailing(MailingEntity $mailing, string $forceRecipient = null): void
    {
        $this->simpleMailer->sendStartSendingMailingMessage($mailing);
        $this->contentProvider->preFetchContent($mailing);

        if ($forceRecipient) {
            $fakeUser = new User();
            $fakeUser->setEmail($forceRecipient);
            $contentMessage = $this->contentProvider->getContentMailing($mailing, $fakeUser);
            $this->logger->debug("Mailing Mailer sends {$contentMessage->getSubject()}.");
            $this->sendMessage($contentMessage);
        } else {
            $campaign = $mailing->getCampaign();
            $this->logger->debug("Mailing Mailer sends Mailing {$mailing->getName()}");
            $recipientCounts = 0;
            foreach ($campaign->getMailingLists() as $mailingList) {
                foreach ($mailingList->getApprovedRegistrations() as $registration) {
                    $contentMessage = $this->contentProvider->getContentMailing($mailing, $registration->getUser());
                    $this->sendMessage($contentMessage);
                    ++$recipientCounts;
                }
            }
            $this->logger->debug("Mailing {$mailing->getName()} induced {$recipientCounts} emails sent.");
        }
        $this->simpleMailer->sendStopSendingMailingMessage($mailing);
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
