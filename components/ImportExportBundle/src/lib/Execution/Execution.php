<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Execution;

use AlmaviaCX\Bundle\IbexaImportExport\Job\Job;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowState;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AlmaviaCX\Bundle\IbexaImportExport\Execution\ExecutionRepository")
 * @ORM\Table(name="import_export_execution")
 */
class Execution
{
    public const STATUS_PENDING = 0;
    public const STATUS_RUNNING = 1;
    public const STATUS_COMPLETED = 2;
    public const STATUS_QUEUED = 3;
    public const STATUS_PAUSED = 4;
    public const STATUS_FORCE_PAUSED = 6;
    public const STATUS_CANCELED = 5;
    public const STATUS_ERROR = 7;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @ORM\Column
     */
    protected ?int $id = null;

    /**
     * @ORM\OneToMany(
     *     targetEntity=ExecutionRecord::class,
     *     mappedBy="execution",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true,
     *     fetch="EXTRA_LAZY",
     *     indexBy="identifier"
     * )
     *
     * @var Collection<string, ExecutionRecord>
     */
    protected Collection $loggerRecords;

    /**
     * @ORM\OneToOne(
     *     targetEntity=WorkflowState::class,
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true,
     *     fetch="EXTRA_LAZY"
     * )
     * @ORM\JoinColumn(name="workflow_state_id", referencedColumnName="id")
     */
    protected WorkflowState $workflowState;

    /**
     * @ORM\Column
     */
    protected int $status = self::STATUS_PENDING;

    /**
     * @ORM\ManyToOne(
     *     targetEntity=Job::class,
     *     inversedBy="executions",
     *     fetch="EXTRA_LAZY"
     * )
     * @ORM\JoinColumn(name="job_id", referencedColumnName="id")
     */
    protected Job $job;

    /**
     * @ORM\Column(type="object")
     */
    protected ExecutionOptions $options;

    public function __construct(
        ExecutionOptions $options = new ExecutionOptions()
    ) {
        $this->loggerRecords = new ArrayCollection();
        $this->workflowState = new WorkflowState();
        $this->options = $options;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getJob(): Job
    {
        return $this->job;
    }

    public function setJob(Job $job): void
    {
        $this->job = $job;
    }

    public function getOptions(): ExecutionOptions
    {
        return $this->options;
    }

    /**
     * @return Collection<string, ExecutionRecord>
     */
    public function getLoggerRecords(): Collection
    {
        return $this->loggerRecords;
    }

    /**
     * @return \Doctrine\Common\Collections\Selectable<string, ExecutionRecord>|null
     */
    public function getRecordsForLevel(int $level): ?Selectable
    {
        $criteria = new Criteria();
        $criteria->where(new Comparison('level', '=', $level));

        return $this->loggerRecords instanceof Selectable ? $this->loggerRecords->matching($criteria) : null;
    }

    /**
     * @param array<ExecutionRecord> $loggerRecords
     */
    public function setLoggerRecords(array $loggerRecords): void
    {
        $this->loggerRecords->clear();
        foreach ($loggerRecords as $record) {
            $this->addLoggerRecord($record);
        }
    }

    /**
     * @param array<ExecutionRecord> $records
     */
    public function addLoggerRecords(array $records): void
    {
        foreach ($records as $record) {
            $this->addLoggerRecord($record);
        }
    }

    public function addLoggerRecord(ExecutionRecord $record): void
    {
        if (null === $record->getExecution()) {
            $record->setExecution($this);
            $this->loggerRecords->set($record->getIdentifier(), $record);
        }
    }

    public function getWorkflowState(): WorkflowState
    {
        return $this->workflowState;
    }

    public function setWorkflowState(WorkflowState $workflowState): void
    {
        $this->workflowState = $workflowState;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function isRunning(): bool
    {
        return self::STATUS_RUNNING === $this->status;
    }

    public function isPaused(): bool
    {
        return in_array($this->status, [self::STATUS_PAUSED, self::STATUS_FORCE_PAUSED]);
    }

    public function isPending(): bool
    {
        return self::STATUS_PENDING === $this->status || $this->isPaused();
    }

    public function isCancelable(): bool
    {
        return !in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELED, self::STATUS_ERROR]);
    }

    public function canRun(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_QUEUED]) || $this->isPaused();
    }

    public function isDone(): bool
    {
        return in_array($this->getStatus(), [self::STATUS_COMPLETED, self::STATUS_CANCELED, self::STATUS_ERROR]);
    }

    public function getCreatorId(): int
    {
        return $this->job->getCreatorId();
    }

    public function getWorkflowIdentifier(): string
    {
        return $this->job->getWorkflowIdentifier();
    }
}
