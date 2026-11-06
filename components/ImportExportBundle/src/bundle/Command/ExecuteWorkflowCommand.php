<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExportBundle\Command;

use AlmaviaCX\Bundle\IbexaImportExport\Execution\ExecutionOptionsFormType;
use AlmaviaCX\Bundle\IbexaImportExport\Monolog\WorkflowConsoleLogger;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowConfiguration;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowEvent;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowExecutor;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowRegistry;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExecuteWorkflowCommand extends Command
{
    protected static $defaultName = 'import_export:workflow:execute';

    public function __construct(
        protected WorkflowRegistry $workflowRegistry,
        protected WorkflowExecutor $workflowExecutor
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();
        $this->addArgument('identifier', InputArgument::REQUIRED, 'Workflow identifier');
        $this->addOption('debug', null, InputOption::VALUE_NONE, 'Enable debug mode');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $workflowIdentifier = $input->getArgument('identifier');
        $workflow = $this->workflowRegistry->getWorkflow($workflowIdentifier);
        $baseConfiguration = $workflow->getDefaultConfig();
        if (!$baseConfiguration->isAvailable(WorkflowConfiguration::AVAILABILITY_CLI)) {
            throw new InvalidArgumentException(sprintf('Workflow %s is not available', $workflowIdentifier));
        }

        /** @var \Matthias\SymfonyConsoleForm\Console\Helper\FormHelper $formHelper */
        $formHelper = $this->getHelper('form');
        $runtimeProcessConfiguration = $formHelper->interactUsingForm(
            ExecutionOptionsFormType::class,
            $input,
            $output,
            ['default_configuration' => $baseConfiguration->getProcessConfiguration()]
        );

        $progressBar = new ProgressBar($output);

        $workflow->addEventListener(WorkflowEvent::START, function (WorkflowEvent $event) use ($progressBar) {
            $progressBar->start($event->getWorkflow()->getState()->getTotalItemsCount());
        });
        $workflow->addEventListener(WorkflowEvent::PROGRESS, function () use ($progressBar) {
            $progressBar->advance();
        });
        $logger = new WorkflowConsoleLogger($output);
        $workflow->setLogger($logger);
        $workflow->setDebug($input->getOption('debug'));
        ($this->workflowExecutor)($workflow, $runtimeProcessConfiguration);
        $workflow->clean();

        return Command::SUCCESS;
    }
}
