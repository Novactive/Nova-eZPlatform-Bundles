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

namespace Novactive\Bundle\eZMailingBundle\Command;

use Doctrine\ORM\EntityManager;
use Novactive\Bundle\eZMailingBundle\Core\Mailer\Mailing as MailingMailer;
use Novactive\Bundle\eZMailingBundle\Entity\Mailing;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class SendTestMailingCommand.
 */
class SendTestMailingCommand extends Command
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var MailingMailer
     */
    protected $mailingMailer;

    /**
     * SendTestMailingCommand constructor.
     *
     * @param EntityManager $entityManager
     * @param MailingMailer $mailingMailer
     */
    public function __construct(EntityManager $entityManager, MailingMailer $mailingMailer)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->mailingMailer = $mailingMailer;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('novaezmailing:test:send:mailing')
            ->setDescription('Send a mailing to an specific email')
            ->setHidden(true)
            ->addArgument('mailingId', InputArgument::REQUIRED, 'The Mailing Id')
            ->addArgument('recipient', InputArgument::REQUIRED, "The recipient's email address");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        /** @var Mailing $mailing */
        $mailing        = $this->entityManager->getRepository('NovaeZMailingBundle:Mailing')->findOneById(
            $input->getArgument('mailingId')
        );
        $recipientEmail = $input->getArgument('recipient');
        $io->title("Sending Mailing: {$mailing->getName()} to {$recipientEmail}");
        $this->mailingMailer->sendMailing($mailing, $recipientEmail);
    }
}
