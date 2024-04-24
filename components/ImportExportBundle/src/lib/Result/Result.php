<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Result;

use DateInterval;
use DateTimeImmutable;

class Result
{
    protected DateTimeImmutable $startTime;
    protected DateTimeImmutable $endTime;
    protected DateInterval $elapsed;
    protected array $writerResults;

    public function __construct(
        DateTimeImmutable $startTime,
        DateTimeImmutable $endTime,
        array $writerResults
    ) {
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->elapsed = $startTime->diff($endTime);
        $this->writerResults = $writerResults;
    }

    public function getStartTime(): DateTimeImmutable
    {
        return $this->startTime;
    }

    public function getEndTime(): DateTimeImmutable
    {
        return $this->endTime;
    }

    public function getElapsed(): DateInterval
    {
        return $this->elapsed;
    }

    public function getWriterResults(): array
    {
        return $this->writerResults;
    }
}
