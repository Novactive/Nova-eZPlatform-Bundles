<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Message;

abstract class JobRunMessage
{
    protected int $jobId;
    protected int $batchLimit;

    public function __construct(int $jobId, int $batchLimit = -1)
    {
        $this->jobId = $jobId;
        $this->batchLimit = $batchLimit;
    }

    public function getJobId(): int
    {
        return $this->jobId;
    }

    public function getBatchLimit(): int
    {
        return $this->batchLimit;
    }
}
