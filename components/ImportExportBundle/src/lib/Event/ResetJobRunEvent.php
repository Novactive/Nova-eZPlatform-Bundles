<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Event;

use AlmaviaCX\Bundle\IbexaImportExport\Job\Job;

class ResetJobRunEvent
{
    protected Job $job;

    public function __construct(Job $job)
    {
        $this->job = $job;
    }

    public function getJob(): Job
    {
        return $this->job;
    }
}
