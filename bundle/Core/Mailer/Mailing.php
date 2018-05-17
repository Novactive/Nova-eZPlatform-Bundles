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

use Novactive\Bundle\eZMailingBundle\Core\Provider\Broadcast;
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
     * @var Broadcast
     */
    private $broadcastProvider;

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
     * @param Broadcast       $broadcastProvider
     */
    public function __construct(
        Simple $simpleMailer,
        MailingContent $contentProvider,
        LoggerInterface $logger,
        Broadcast $broadcastProvider
    ) {
        $this->simpleMailer      = $simpleMailer;
        $this->contentProvider   = $contentProvider;
        $this->logger            = $logger;
        $this->broadcastProvider = $broadcastProvider;
    }

    /**
     * @param MailingEntity $mailing
     */
    public function sendMailing(MailingEntity $mailing, string $forceRecipient = null): void
    {
        $nativeHtml = $this->contentProvider->preFetchContent($mailing);
        $broadcast  = $this->broadcastProvider->start($mailing, $nativeHtml);

        $this->simpleMailer->sendStartSendingMailingMessage($mailing);
        if ($forceRecipient) {
            $fakeUser = new User();
            $fakeUser->setEmail($forceRecipient);
            $fakeUser->setFirstName('XXXX');
            $fakeUser->setLastName('YYYY');
            $contentMessage = $this->contentProvider->getContentMailing($mailing, $fakeUser, $broadcast);
            $this->logger->debug("Mailing Mailer starts to test {$contentMessage->getSubject()}.");
            $this->sendMessage($contentMessage);
        } else {
            $campaign = $mailing->getCampaign();
            $this->logger->notice("Mailing Mailer starts to send Mailing {$mailing->getName()}");
            $recipientCounts = 0;

            foreach ($campaign->getMailingLists() as $mailingList) {
                foreach ($mailingList->getValidRecipients() as $user) {
                    /** @var User $user */
                    $contentMessage = $this->contentProvider->getContentMailing($mailing, $user, $broadcast);
                    $this->sendMessage($contentMessage);
                    ++$recipientCounts;

                    if (0 === $recipientCounts % 10) {
                        $broadcast->setEmailSentCount($recipientCounts);
                        $this->broadcastProvider->store($broadcast);
                    }
                }
                $this->broadcastProvider->store($broadcast);
            }
            $this->broadcastProvider->store($broadcast);
            $this->logger->notice("Mailing {$mailing->getName()} induced {$recipientCounts} emails sent.");
        }
        $this->simpleMailer->sendStopSendingMailingMessage($mailing);
        $this->broadcastProvider->end($broadcast);
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
