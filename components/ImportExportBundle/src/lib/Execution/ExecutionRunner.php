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
            $execution = $this->updateExecution($execution, $workflow);
            $event->setContinue($execution->isRunning());
        };
        $workflow->addEventListener(WorkflowEvent::PROGRESS, $onWorkflowProgress);
        $workflow->addEventListener(WorkflowEvent::START, $onWorkflowProgress);

        $this->eventDispatcher->dispatch(new PreJobRunEvent($execution, $workflow));

        $workflow->setState($execution->getWorkflowState());
        $execution->setStatus(Execution::STATUS_RUNNING);
        $this->executionRepository->save($execution);

        ($this->workflowExecutor)(
            $workflow,
            $this->buildExecutionOptions($execution),
            $batchLimit
        );

        $execution = $this->updateExecution($execution, $workflow);
        if ($workflow->getState()->isCompleted()) {
            $execution->setStatus(Execution::STATUS_COMPLETED);
        } elseif ($execution->isRunning()) {
            $execution->setStatus(Execution::STATUS_PAUSED);
        }

        $this->eventDispatcher->dispatch(new PostJobRunEvent($execution, $workflow));

        $this->executionRepository->save($execution);

        return $execution->getStatus();
    }

    protected function buildExecutionOptions(Execution $execution): ExecutionOptions
    {
        $executionOptions = $execution->getOptions();
        $jobOptions = $execution->getJob()->getOptions();

        return $jobOptions->merge($executionOptions);
    }

    protected function updateExecution(Execution $execution, WorkflowInterface $workflow): Execution
    {
        /**
         * We refresh the execution because it might have been paused or canceled during the workflow execution.
         */
        $state = $workflow->getState();
        try {
            $this->executionRepository->refresh($execution);
        } catch (ORMInvalidArgumentException $exception) {
            $execution = $this->executionRepository->findById($execution->getId());
        }
        $existingState = $execution->getWorkflowState();

        /*
         * Ibexa content creations sometimes trigger an entity manager clear
         * This means that the state from workflow and the state from the execution are the same entity but different php object.
         */
        if ($state !== $existingState) {
            $existingState->setStartTime($state->getStartTime());
            $existingState->setEndTime($state->getEndTime());
            $existingState->setTotalItemsCount($state->getTotalItemsCount());
            $existingState->setOffset($state->getOffset());
            $existingState->setWritersResults($state->getWritersResults());
            $existingState->setReferenceBag($state->getReferenceBag());
            $existingState->setCache($state->getCache());
            $this->em->persist($existingState);
        } else {
            $classMetadata = $this->em->getClassMetadata(WorkflowState::class);
            $this->em->getUnitOfWork()->computeChangeSet($classMetadata, $state);
            $this->em->getUnitOfWork()->propertyChanged($state, 'writersResults', null, $state->getWritersResults());

            $this->em->getUnitOfWork()->propertyChanged($state, 'referenceBag', null, $state->getReferenceBag());

            $this->em->getUnitOfWork()->propertyChanged($state, 'cache', null, $state->getCache());
        }

        if ($workflow->getLogger()) {
            $execution->addLoggerRecords($workflow->getLogger()->getRecords());
        }

        $this->executionRepository->save($execution);

        return $execution;
    }
}
