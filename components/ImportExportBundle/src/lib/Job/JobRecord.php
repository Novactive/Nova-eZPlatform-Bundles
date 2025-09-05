<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Job;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;

/**
 * @ORM\Entity
 *
 * @ORM\Table(name="import_export_job_record")
 *
 * @phpstan-import-type Record from \Monolog\Logger
 */
class JobRecord
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(type="ulid")
     */
    protected Ulid $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected string $identifier;

    /**
     * @ORM\Column(type="object")
     *
     * @var Record
     */
    protected array $record;

    /**
     * @ORM\Column
     */
    protected int $level;

    /**
     * @ORM\ManyToOne(targetEntity=Job::class, inversedBy="records")
     *
     * @ORM\JoinColumn(name="job_id", referencedColumnName="id")
     */
    protected Job $job;

    public function __construct(Ulid $id, array $record)
    {
        $this->id = $id;
        $this->identifier = $id->toRfc4122();
        $this->record = $record;
        $this->level = $record['level'];
    }

    public function getId(): Ulid
    {
        return $this->id;
    }

    public function setId(Ulid $id): void
    {
        $this->id = $id;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * @return Record
     */
    public function getRecord(): array
    {
        return $this->record;
    }

    /**
     * @param Record $record
     */
    public function setRecord(array $record): void
    {
        $this->record = $record;
    }

    public function getJob(): Job
    {
        return $this->job;
    }

    public function setJob(Job $job): void
    {
        $this->job = $job;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): void
    {
        $this->level = $level;
    }
}
