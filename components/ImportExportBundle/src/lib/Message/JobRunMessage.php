<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Message;

abstract class JobRunMessage
{
    public function __construct(
        protected int $executionId,
        protected int $batchLimit = -1
    ) {
    }

    public function getExecutionId(): int
    {
        return $this->executionId;
    }

    public function getBatchLimit(): int
    {
        return $this->batchLimit;
    }
}
