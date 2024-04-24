<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Job;

use AlmaviaCX\Bundle\IbexaImportExport\Event\PostJobRunEvent;
use AlmaviaCX\Bundle\IbexaImportExport\Event\PreJobRunEvent;
use AlmaviaCX\Bundle\IbexaImportExport\Monolog\WorkflowLogger;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowEvent;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowExecutor;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowRegistry;
use DateTimeImmutable;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class JobRunner extends AbstractJobRunner
{
    protected WorkflowExecutor $workflowExecutor;
    protected WorkflowRegistry $workflowRegistry;
    protected EventDispatcherInterface $eventDispatcher;
    protected JobRepository $jobRepository;

    /**
     * @param \AlmaviaCX\Bundle\IbexaImportExport\Job\JobRepository $jobRepository
     */
    public function __construct(
        WorkflowExecutor $workflowExecutor,
        WorkflowRegistry $workflowRegistry,
        EventDispatcherInterface $eventDispatcher,
        JobRepository $jobRepository
    ) {
        $this->workflowExecutor = $workflowExecutor;
        $this->workflowRegistry = $workflowRegistry;
        $this->eventDispatcher = $eventDispatcher;
        $this->jobRepository = $jobRepository;
    }

    protected function run(Job $job): void
    {
        $logger = new WorkflowLogger();

        $workflow = $this->workflowRegistry->getWorkflow($job->getWorkflowIdentifier());
        $workflow->setLogger($logger);
        $workflow->addEventListener(WorkflowEvent::PROGRESS, function (WorkflowEvent $event) use ($logger, $job) {
            $workflow = $event->getWorkflow();

            // Ibexa content creation trigger an entity manager clear, which mean we need to reload the entity
            $job = $this->jobRepository->findById($job->getId());
            $job->addRecords($logger->getRecords());
            $job->setProgress($workflow->getProgress());
            $this->jobRepository->save($job);
        });

        $this->eventDispatcher->dispatch(new PreJobRunEvent($job, $workflow));

        $job->setStatus(Job::STATUS_RUNNING);
        $job->setStartTime(new DateTimeImmutable());
        $this->jobRepository->save($job);

        $results = ($this->workflowExecutor)(
            $workflow,
            $job->getOptions()
        );

        // Ibexa content creation trigger an entity manager clear, which mean we need to reload the entity
        $job = $this->jobRepository->findById($job->getId());
        $job->setStatus(Job::STATUS_COMPLETED);
        $job->setEndTime($results->getEndTime());
        $job->addRecords($logger->getRecords());
        $job->setWriterResults($results->getWriterResults());

        $this->eventDispatcher->dispatch(new PostJobRunEvent($job, $workflow, $results));

        $this->jobRepository->save($job);
    }
}
