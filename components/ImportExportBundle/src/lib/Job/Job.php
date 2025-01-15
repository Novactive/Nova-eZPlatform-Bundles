<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Job;

use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorOptions;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderOptions;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AlmaviaCX\Bundle\IbexaImportExport\Job\JobRepository")
 * @ORM\Table(name="import_export_job")
 */
class Job
{
    public const STATUS_PENDING = 0;
    public const STATUS_RUNNING = 1;
    public const STATUS_COMPLETED = 2;
    public const STATUS_QUEUED = 3;
    public const STATUS_PAUSED = 4;
    public const STATUS_CANCELED = 5;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @ORM\Column
     */
    protected ?int $id = null;

    /**
     * @ORM\Column
     */
    protected string $label;

    /**
     * @ORM\Column
     */
    protected ?string $workflowIdentifier = null;

    /**
     * @ORM\Column
     */
    protected int $status = self::STATUS_PENDING;

    /**
     * @ORM\Column
     */
    protected int $processedItemsCount = 0;

    /**
     * @ORM\Column
     */
    protected int $totalItemsCount = 0;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    protected DateTimeImmutable $requestedDate;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    protected ?DateTimeImmutable $startTime = null;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    protected ?DateTimeImmutable $endTime = null;

    /**
     * @ORM\Column
     */
    protected int $creatorId;

    /**
     * @ORM\Column(type="object")
     *
     * @var array{reader?: ReaderOptions, processors?: array<mixed, ProcessorOptions>}
     */
    protected array $options = [];

    /**
     * @ORM\OneToMany(
     *     targetEntity=JobRecord::class,
     *     mappedBy="job",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true,
     *     fetch="EXTRA_LAZY",
     *     indexBy="identifier"
     * )
     *
     * @var Collection<JobRecord>
     */
    protected ?Collection $records = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected ?string $writerResults = null;

    public function __construct()
    {
        $this->records = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getWorkflowIdentifier(): ?string
    {
        return $this->workflowIdentifier;
    }

    public function setWorkflowIdentifier(?string $workflowIdentifier): void
    {
        $this->workflowIdentifier = $workflowIdentifier;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function getRequestedDate(): DateTimeImmutable
    {
        return $this->requestedDate;
    }

    public function setRequestedDate(DateTimeImmutable $requestedDate): void
    {
        $this->requestedDate = $requestedDate;
    }

    public function getStartTime(): ?DateTimeImmutable
    {
        return $this->startTime;
    }

    public function setStartTime(?DateTimeImmutable $startTime): void
    {
        $this->startTime = $startTime;
    }

    public function getEndTime(): ?DateTimeImmutable
    {
        return $this->endTime;
    }

    public function setEndTime(?DateTimeImmutable $endTime): void
    {
        $this->endTime = $endTime;
    }

    public function getCreatorId(): int
    {
        return $this->creatorId;
    }

    public function setCreatorId(int $creatorId): void
    {
        $this->creatorId = $creatorId;
    }

    /**
     * @return array{reader?: ReaderOptions, processors?: array<mixed, ProcessorOptions>}
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array{reader?: ReaderOptions, processors?: array<mixed, ProcessorOptions>} $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection<JobRecord>
     */
    public function getRecords(): Collection
    {
        return $this->records;
    }

    public function getRecordsForLevel(int $level): Collection
    {
        $criteria = new Criteria();
        $criteria->where(new Comparison('level', '=', $level));

        return $this->records->matching($criteria);
    }

    /**
     * @param array<JobRecord> $records
     */
    public function setRecords(array $records): void
    {
        $this->records->clear();
        foreach ($records as $record) {
            $this->addRecord($record);
        }
    }

    /**
     * @param array<JobRecord> $records
     */
    public function addRecords(array $records): void
    {
        foreach ($records as $record) {
            $this->addRecord($record);
        }
    }

    public function addRecord(JobRecord $record): void
    {
        if (!$this->records) {
            $this->records = new ArrayCollection();
        }
        if (!$this->records->containsKey($record->getIdentifier())) {
            $record->setJob($this);
            $this->records->set($record->getIdentifier(), $record);
        }
    }

    /**
     * @return \AlmaviaCX\Bundle\IbexaImportExport\Writer\WriterResults[]
     */
    public function getWriterResults(): array
    {
        return $this->writerResults ? unserialize($this->writerResults) : [];
    }

    public function setWriterResults(array $writerResults): void
    {
        $this->writerResults = serialize($writerResults);
    }

    public function getProcessedItemsCount(): int
    {
        return $this->processedItemsCount;
    }

    public function setProcessedItemsCount(int $processedItemsCount): void
    {
        $this->processedItemsCount = $processedItemsCount;
    }

    public function getTotalItemsCount(): int
    {
        return $this->totalItemsCount;
    }

    public function setTotalItemsCount(int $totalItemsCount): void
    {
        $this->totalItemsCount = $totalItemsCount;
    }

    public function getProgress(): float
    {
        return $this->totalItemsCount > 0 ? $this->processedItemsCount / $this->totalItemsCount : 0;
    }

    public function reset(): void
    {
        $this->startTime = null;
        $this->endTime = null;
        $this->records = new ArrayCollection();
        $this->writerResults = null;
        $this->status = self::STATUS_PENDING;
        $this->totalItemsCount = 0;
        $this->processedItemsCount = 0;
    }
}
