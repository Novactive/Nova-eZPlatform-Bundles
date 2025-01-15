<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExportBundle\Command;

use AlmaviaCX\Bundle\IbexaImportExport\Job\JobService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunJobCommand extends Command
{
    protected static $defaultName = 'import_export:job:run';

    public function __construct(
        protected JobService $jobService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();
        $this->addArgument('job_id', InputArgument::REQUIRED, 'Job ID');
        $this->addOption('batch_limit', 'l', InputOption::VALUE_OPTIONAL, 'Batch limit', 50);
        $this->addOption('reset', null, InputOption::VALUE_NONE, 'Reset');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $job = $this->jobService->loadJobById($input->getArgument('job_id'));
        $this->jobService->runJob($job, $input->getOption('batch_limit'), $input->getOption('reset'));

        return Command::SUCCESS;
    }
}
