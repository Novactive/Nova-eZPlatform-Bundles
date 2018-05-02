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

use Novactive\Bundle\eZMailingBundle\Core\Processor\SendMailing;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class CronRunCommand.
 */
class SendMailingCommand extends Command
{
    /**
     * @var SendMailing
     */
    private $processor;

    /**
     * SendMailingCommand constructor.
     *
     * @param SendMailing $processor
     */
    public function __construct(SendMailing $processor)
    {
        parent::__construct();
        $this->processor = $processor;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('novaezmailing:send:mailing')
            ->setDescription('Send all the mailings according to their sending rules.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Process the mailings');
        $this->processor->execute();
        $io->success('Done.');
    }
}
