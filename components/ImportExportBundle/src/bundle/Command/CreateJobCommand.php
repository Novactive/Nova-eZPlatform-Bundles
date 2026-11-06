<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExportBundle\Command;

use AlmaviaCX\Bundle\IbexaImportExport\Event\PostJobCreateFormSubmitEvent;
use AlmaviaCX\Bundle\IbexaImportExport\Job\Form\Type\JobProcessConfigurationFormType;
use AlmaviaCX\Bundle\IbexaImportExport\Job\Job;
use AlmaviaCX\Bundle\IbexaImportExport\Job\JobService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CreateJobCommand extends Command
{
    protected static $defaultName = 'import_export:job:create';

    public function __construct(
        protected JobService $jobService,
        protected EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();
        $this->addArgument('identifier', InputArgument::REQUIRED, 'Workflow identifier');
        $this->addArgument('label', InputArgument::REQUIRED, 'Job label');
        $this->addArgument('creator', InputArgument::REQUIRED, 'Creator');
        $this->addOption('debug', null, InputOption::VALUE_NONE, 'Enable debug mode');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $job = new Job();
        $job->setLabel($input->getArgument('label'));
        $job->setCreatorId((int) $input->getArgument('creator'));
        $job->setWorkflowIdentifier($input->getArgument('identifier'));

        /** @var \Matthias\SymfonyConsoleForm\Console\Helper\FormHelper $formHelper */
        $formHelper = $this->getHelper('form');

        $job = $formHelper->interactUsingForm(
            JobProcessConfigurationFormType::class,
            $input,
            $output,
            [],
            $job
        );

        $this->eventDispatcher->dispatch(new PostJobCreateFormSubmitEvent($job));
        $this->jobService->createJob($job, false);

        return Command::SUCCESS;
    }
}
