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
     * Mailing constructor.
     *
     * @param Simple         $simpleMailer
     * @param MailingContent $contentProvider
     */
    public function __construct(Simple $simpleMailer, MailingContent $contentProvider)
    {
        $this->simpleMailer    = $simpleMailer;
        $this->contentProvider = $contentProvider;
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
            $this->sendMessage($contentMessage);
        } else {
            $campaign = $mailing->getCampaign();
            foreach ($campaign->getMailingLists() as $mailingList) {
                foreach ($mailingList->getApprovedRegistrations() as $registration) {
                    $contentMessage = $this->contentProvider->getContentMailing($mailing, $registration->getUser());
                    $this->sendMessage($contentMessage);
                }
            }
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
