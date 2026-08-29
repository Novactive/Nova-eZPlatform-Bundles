<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Event;

use AlmaviaCX\Bundle\IbexaImportExport\Job\Job;

class PostJobCreateFormSubmitEvent
{
    public function __construct(
        protected Job $job
    ) {
    }

    public function getJob(): Job
    {
        return $this->job;
    }
}
