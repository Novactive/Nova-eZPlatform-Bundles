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

use Doctrine\ORM\EntityManager;
use Novactive\Bundle\eZMailingBundle\Core\Mailer\Mailing as MailingMailer;
use Novactive\Bundle\eZMailingBundle\Entity\Mailing;

/**
 * Class SendMailing.
 */
class TestMailing extends Processor
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
     * @param int    $mailingId
     * @param string $email
     */
    public function execute(int $mailingId, string $email): void
    {
        /** @var Mailing $mailing */
        $mailing = $this->entityManager->getRepository('NovaeZMailingBundle:Mailing')->findOneById($mailingId);
        $this->mailingMailer->sendMailing($mailing, $email);
    }
}
