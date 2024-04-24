<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Message;

class JobRunMessage
{
    protected int $jobId;

    public function __construct(int $jobId)
    {
        $this->jobId = $jobId;
    }

    public function getJobId(): int
    {
        return $this->jobId;
    }
}
