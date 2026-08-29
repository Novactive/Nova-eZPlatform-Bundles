<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Job;

use AlmaviaCX\Bundle\IbexaImportExport\Execution\Execution;
use AlmaviaCX\Bundle\IbexaImportExport\Execution\ExecutionOptions;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;

/**
 * @ORM\Entity(repositoryClass="AlmaviaCX\Bundle\IbexaImportExport\Job\JobRepository")
 * @ORM\Table(name="import_export_job")
 */
class Job
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @ORM\Column
     */
    protected ?int $id = null;

    /**
     * @ORM\Column(type="ulid", unique=true, nullable=true)
     */
    protected ?Ulid $ulid = null;

    /**
     * @ORM\Column
     */
    protected string $label;

    /**
     * @ORM\Column
     */
    protected ?string $workflowIdentifier = null;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    protected DateTimeImmutable $requestedDate;

    /**
     * @ORM\Column
     */
    protected int $creatorId;

    /**
     * @ORM\Column(type="object")
     */
    protected ExecutionOptions $options;

    /**
     * @ORM\OneToMany(
     *     targetEntity=Execution::class,
     *     mappedBy="job",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true,
     *     fetch="EXTRA_LAZY"
     * )
     *
     * @var Collection<int, Execution>
     */
    protected Collection $executions;

    public function __construct(ExecutionOptions $options = new ExecutionOptions())
    {
        $this->executions = new ArrayCollection();
        $this->options = $options;
        $this->ulid = new Ulid();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUlid(): ?Ulid
    {
        return $this->ulid;
    }

    public function setUlid(?Ulid $ulid): void
    {
        $this->ulid = $ulid;
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

    public function getRequestedDate(): DateTimeImmutable
    {
        return $this->requestedDate;
    }

    public function setRequestedDate(DateTimeImmutable $requestedDate): void
    {
        $this->requestedDate = $requestedDate;
    }

    public function getCreatorId(): int
    {
        return $this->creatorId;
    }

    public function setCreatorId(int $creatorId): void
    {
        $this->creatorId = $creatorId;
    }

    public function getOptions(): ExecutionOptions
    {
        return $this->options;
    }

    public function setOptions(ExecutionOptions $options): void
    {
        $this->options = $options;
    }

    /**
     * @return Collection<int, Execution>
     */
    public function getExecutions(): Collection
    {
        return $this->executions;
    }

    /**
     * @param Collection<int, Execution> $executions
     */
    public function setExecutions(Collection $executions): void
    {
        $this->executions = $executions;
    }

    public function addExecution(Execution $execution): void
    {
        $execution->setJob($this);
        $this->executions->add($execution);
    }

    public function getLastExecution(): Execution|false
    {
        $criteria = new Criteria();
        $criteria->orderBy(['id' => Criteria::DESC]);
        $criteria->setMaxResults(1);

        return $this->executions->matching($criteria)->first();
    }

    public function getExecutionCount(): int
    {
        $criteria = new Criteria();
        $criteria->setMaxResults(1);

        return $this->executions->matching($criteria)->count();
    }

    public function getPendingExecutionCount(): int
    {
        $criteria = new Criteria();
        $criteria->where(
            new Comparison('status', Comparison::IN, [Execution::STATUS_PENDING, Execution::STATUS_QUEUED])
        );
        $criteria->setMaxResults(1);

        return $this->executions->matching($criteria)->count();
    }
}
