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

namespace Novactive\Bundle\eZMailingBundle\Core\Processor;

use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Novactive\Bundle\eZMailingBundle\Core\Mailer\Mailing as MailingMailer;
use Novactive\Bundle\eZMailingBundle\Core\Utils\Clock;
use Novactive\Bundle\eZMailingBundle\Entity\Mailing;

/**
 * Class SendMailing.
 */
class SendMailing extends Processor
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var MailingMailer
     */
    private $mailingMailer;

    /**
     * SendMailingCommand constructor.
     *
     * @param EntityManager $entityManager
     * @param MailingMailer $mailingMailer
     */
    public function __construct(EntityManager $entityManager, MailingMailer $mailingMailer)
    {
        $this->entityManager = $entityManager;
        $this->mailingMailer = $mailingMailer;
    }

    /**
     * Send the mailings.
     */
    public function execute(): void
    {
        $mailingRepository = $this->entityManager->getRepository('NovaeZMailingBundle:Mailing');
        $pendingMailings   = $mailingRepository->findByStatus(Mailing::PENDING);
        $clock             = new Clock(Carbon::now());
        $matched           = $sent = 0;
        foreach ($pendingMailings as $mailing) {
            /** @var Mailing $mailing */
            if ($clock->match($mailing)) {
                ++$matched;
                $this->logger->info("{$mailing->getName()} has been matched pending and rending to send.");
                $this->mailingMailer->sendMailing($mailing);
                ++$sent;
            }
        }
        $this->logger->info("{$matched} matched mailings induced {$sent} sendings.");
    }
}
