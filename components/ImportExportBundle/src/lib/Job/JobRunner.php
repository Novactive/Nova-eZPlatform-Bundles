<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Job;

use AlmaviaCX\Bundle\IbexaImportExport\Event\PostJobRunEvent;
use AlmaviaCX\Bundle\IbexaImportExport\Event\PreJobRunEvent;
use AlmaviaCX\Bundle\IbexaImportExport\Monolog\WorkflowLogger;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowEvent;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowExecutor;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowRegistry;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class JobRunner extends AbstractJobRunner
{
    protected WorkflowExecutor $workflowExecutor;
    protected WorkflowRegistry $workflowRegistry;
    protected JobRepository $jobRepository;

    public function __construct(
        WorkflowExecutor $workflowExecutor,
        WorkflowRegistry $workflowRegistry,
        EventDispatcherInterface $eventDispatcher,
        JobRepository $jobRepository
    ) {
        $this->workflowExecutor = $workflowExecutor;
        $this->workflowRegistry = $workflowRegistry;
        $this->jobRepository = $jobRepository;
        parent::__construct($eventDispatcher);
    }

    protected function run(Job $job, int $batchLimit = -1): int
    {
        $logger = new WorkflowLogger();

        $workflow = $this->workflowRegistry->getWorkflow($job->getWorkflowIdentifier());
        $workflow->setLogger($logger);

        $proccessed = 0;
        $onWorkflowProgress = function (WorkflowEvent $event) use (&$proccessed, $logger, $job) {
            $workflow = $event->getWorkflow();
            // Ibexa content creation trigger an entity manager clear, which mean we need to reload the entity
            $job = $this->jobRepository->findById($job->getId());
            $event->setContinue(Job::STATUS_RUNNING === $job->getStatus());
            $job->addRecords($logger->getRecords());
            $job->setProcessedItemsCount($workflow->getOffset());
            $this->jobRepository->save($job);
            ++$proccessed;
        };
        $workflow->addEventListener(WorkflowEvent::PROGRESS, $onWorkflowProgress);

        $this->eventDispatcher->dispatch(new PreJobRunEvent($job, $workflow));

        if ($job->isPaused()) {
            $workflow->setOffset($job->getProcessedItemsCount());
            $workflow->setWriterResults($job->getWriterResults());
            $workflow->setTotalItemsCount($job->getTotalItemsCount());
        } else {
            $job->setStartTime(new \DateTimeImmutable());
            $onWorkflowStart = function (WorkflowEvent $event) use ($job) {
                $workflow = $event->getWorkflow();
                // Ibexa content creation trigger an entity manager clear, which mean we need to reload the entity
                $job = $this->jobRepository->findById($job->getId());
                $job->setTotalItemsCount($workflow->getTotalItemsCount());
                $this->jobRepository->save($job);
            };
            $workflow->addEventListener(WorkflowEvent::START, $onWorkflowStart);
        }
        $job->setStatus(Job::STATUS_RUNNING);
        $this->jobRepository->save($job);

        ($this->workflowExecutor)(
            $workflow,
            $job->getOptions(),
            $batchLimit
        );

        // Ibexa content creation trigger an entity manager clear, which mean we need to reload the entity
        $job = $this->jobRepository->findById($job->getId());
        $job->addRecords($logger->getRecords());
        $job->setWriterResults($workflow->getWriterResults());
        if (1 == $job->getProgress() || 0 === $job->getTotalItemsCount() || 0 === $proccessed) {
            $job->setStatus(Job::STATUS_COMPLETED);
            $job->setEndTime($workflow->getEndTime());
        } elseif (Job::STATUS_RUNNING === $job->getStatus()) {
            $job->setStatus(Job::STATUS_PAUSED);
        }

        $this->eventDispatcher->dispatch(new PostJobRunEvent($job, $workflow));

        $this->jobRepository->save($job);

        return $job->getStatus();
    }
}
