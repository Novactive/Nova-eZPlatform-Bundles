<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Execution;

use AlmaviaCX\Bundle\IbexaImportExport\Event\PostJobRunEvent;
use AlmaviaCX\Bundle\IbexaImportExport\Event\PreJobRunEvent;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowEvent;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowExecutor;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowRegistry;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowState;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMInvalidArgumentException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Throwable;

class ExecutionRunner
{
    public function __construct(
        protected WorkflowExecutor $workflowExecutor,
        protected WorkflowRegistry $workflowRegistry,
        protected EventDispatcherInterface $eventDispatcher,
        protected ExecutionRepository $executionRepository,
        protected EntityManagerInterface $em,
    ) {
    }

    public function __invoke(Execution $execution, int $batchLimit = -1): int
    {
        if (!$execution->canRun()) {
            return $execution->getStatus();
        }
        $workflow = $this->workflowRegistry->getWorkflow($execution->getWorkflowIdentifier());

        $onWorkflowProgress = function (WorkflowEvent $event) use ($execution) {
            $workflow = $event->getWorkflow();
            $execution = $this->refreshExecution($execution);
            $this->updateExecutionState($execution, $workflow);
            $event->setContinue($execution->isRunning());
        };
        $workflow->addEventListener(WorkflowEvent::PROGRESS, $onWorkflowProgress);
        $workflow->addEventListener(WorkflowEvent::START, $onWorkflowProgress);

        $this->eventDispatcher->dispatch(new PreJobRunEvent($execution, $workflow));

        $workflow->setState($execution->getWorkflowState());
        $execution->setStatus(Execution::STATUS_RUNNING);
        $this->executionRepository->save($execution);

        try {
            ($this->workflowExecutor)(
                $workflow,
                $this->buildExecutionOptions($execution),
                $batchLimit
            );

            $execution = $this->refreshExecution($execution);
            $this->updateExecutionState($execution, $workflow);
            if ($workflow->getState()->isCompleted()) {
                $execution->setStatus(Execution::STATUS_COMPLETED);
            } elseif ($execution->isRunning()) {
                $execution->setStatus(Execution::STATUS_PAUSED);
            }

            $this->eventDispatcher->dispatch(new PostJobRunEvent($execution, $workflow));
        } catch (Throwable $e) {
            if ($workflow->isDebug()) {
                throw $e;
            }
            $workflow->getLogger()->error($e->getMessage());
            $execution = $this->refreshExecution($execution);
            $this->updateExecutionState($execution, $workflow);
            $execution->setStatus(Execution::STATUS_ERROR);
        }

        $this->executionRepository->save($execution);

        return $execution->getStatus();
    }

    protected function buildExecutionOptions(Execution $execution): ExecutionOptions
    {
        $executionOptions = $execution->getOptions();
        $jobOptions = $execution->getJob()->getOptions();

        return $jobOptions->merge($executionOptions);
    }

    protected function refreshExecution(Execution $execution): Execution
    {
        /*
         * We refresh the execution because it might have been paused or canceled during the workflow execution.
         */

        try {
            $this->executionRepository->refresh($execution);

            return $execution;
        } catch (ORMInvalidArgumentException $exception) {
            return $this->executionRepository->findById($execution->getId());
        }
    }

    protected function updateExecutionState(Execution $execution, WorkflowInterface $workflow): void
    {
        $workflowState = $workflow->getState();
        $executionState = $execution->getWorkflowState();

        /*
         * Ibexa content creations sometimes trigger an entity manager clear
         * This means that the state from workflow and the state from the execution are the same entity but different php object.
         */
        if ($workflowState !== $executionState) {
            $executionState->setStartTime($workflowState->getStartTime());
            $executionState->setEndTime($workflowState->getEndTime());
            $executionState->setTotalItemsCount($workflowState->getTotalItemsCount());
            $executionState->setOffset($workflowState->getOffset());
            $executionState->setWritersResults($workflowState->getWritersResults());
            $executionState->setReferenceBag($workflowState->getReferenceBag());
            $executionState->setCache($workflowState->getCache());
            $this->em->persist($executionState);
        } else {
            $classMetadata = $this->em->getClassMetadata(WorkflowState::class);
            $this->em->getUnitOfWork()->computeChangeSet($classMetadata, $workflowState);
            $this->em->getUnitOfWork()->propertyChanged(
                $workflowState,
                'writersResults',
                null,
                $workflowState->getWritersResults()
            );
            $this->em->getUnitOfWork()->propertyChanged(
                $workflowState,
                'referenceBag',
                null,
                $workflowState->getReferenceBag()
            );
            $this->em->getUnitOfWork()->propertyChanged(
                $workflowState,
                'cache',
                null,
                $workflowState->getCache()
            );
        }

        $logger = $workflow->getLogger();
        if ($logger) {
            $records = $logger->getRecords();
            foreach ($records as $record) {
                $record->setExecution($execution);
                $this->em->persist($record);
            }
            $logger->clearRecords();
        }

        $this->executionRepository->save($execution);
    }
}
