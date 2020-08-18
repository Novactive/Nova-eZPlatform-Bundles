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

use Doctrine\ORM\EntityManagerInterface;
use Novactive\Bundle\eZMailingBundle\Core\Processor\TestMailingProcessorInterface as TestMailing;
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
     * @var TestMailing
     */
    private $processor;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * SendTestMailingCommand constructor.
     */
    public function __construct(TestMailing $processor, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->processor = $processor;
        $this->entityManager = $entityManager;
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
        $mailingId = (int) $input->getArgument('mailingId');
        $recipientEmail = $input->getArgument('recipient');
        $io->title('Sending a Mailing for test');
        $io->writeln("Mailing ID: <comment>{$mailingId}</comment>");
        $io->writeln("To: <comment>{$recipientEmail}</comment>");
        $mailing = $this->entityManager->getRepository(Mailing::class)->findOneById($mailingId);
        $this->processor->execute($mailing, $recipientEmail);
        $io->success('Done.');
    }
}
