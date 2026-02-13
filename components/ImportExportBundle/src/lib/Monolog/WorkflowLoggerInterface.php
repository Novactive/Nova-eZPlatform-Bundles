<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Monolog;

use AlmaviaCX\Bundle\IbexaImportExport\Execution\ExecutionRecord;
use Psr\Log\LoggerInterface;
use Throwable;

interface WorkflowLoggerInterface extends LoggerInterface
{
    public function setItemIndex(?int $itemIndex): void;

    public function logException(Throwable $e): void;

    /**
     * @return array<ExecutionRecord>
     */
    public function getRecords(): array;

    public function clearRecords(): void;
}
