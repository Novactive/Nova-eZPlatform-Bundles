<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExportBundle\Command;

use AlmaviaCX\Bundle\IbexaImportExport\Execution\ExecutionOptions;
use AlmaviaCX\Bundle\IbexaImportExport\Job\JobService;
use AlmaviaCX\Bundle\IbexaImportExport\Monolog\WorkflowConsoleLogger;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowEvent;
use FOS\HttpCache\ProxyClient\Symfony;
use Ibexa\Contracts\Core\Repository\Repository as RepositoryInterface;
use Ibexa\Core\Repository\Repository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function Clue\StreamFilter\fun;

class RunJobCommand extends Command
{
    protected static $defaultName = 'import_export:job:run';

    public function __construct(
        protected JobService $jobService,
        protected RepositoryInterface $repository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();
        $this->addArgument(
            'job_id',
            InputArgument::REQUIRED,
            'Job ID'
        );
        $this->addArgument(
            'execution_id',
            InputArgument::OPTIONAL,
            'Execution ID'
        );
        $this->addOption(
            'batch_limit',
            'l',
            InputOption::VALUE_OPTIONAL,
            'Batch limit',
            -1
        );
        $this->addOption(
            'no-async',
            null,
            InputOption::VALUE_NONE
        );
        $this->addOption(
            'user',
            'u',
            InputOption::VALUE_OPTIONAL,
            'User to used to run the job',
            'admin'
        );
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        parent::initialize($input, $output);

        $userIdentifier = $input->getOption('user');
        $user = $this->repository->getUserService()->loadUserByLogin($userIdentifier);
        $this->repository->getPermissionResolver()->setCurrentUserReference($user);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $async = !$input->getOption('no-async');
        $executionId = $input->getArgument('execution_id');
        $jobId = $input->getArgument('job_id');

        $job = $this->jobService->loadJobById((int) $jobId);

        if ($executionId) {
            $question = sprintf(
                'Confirm you want to start the execution %d for the job "%s"',
                $executionId,
                $job->getLabel()
            );
        } else {
            $question = sprintf(
                'Confirm you want to run the job "%s" with a new execution',
                $job->getLabel()
            );
        }
        if (!$io->confirm($question)) {
            return Command::FAILURE;
        }

        $execution = $this->jobService->executeJob(
            $job,
            new ExecutionOptions(),
            (int) $input->getOption('batch_limit'),
            (int) $executionId,
            $async
        );

        $output->writeln(
            sprintf(
                'Ended execution (%d) of job "%s"',
                $execution->getId(),
                $job->getLabel()
            )
        );

        return Command::SUCCESS;
    }
}
