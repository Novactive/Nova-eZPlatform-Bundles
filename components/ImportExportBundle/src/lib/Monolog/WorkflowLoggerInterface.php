<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Monolog;

use Psr\Log\LoggerInterface;

interface WorkflowLoggerInterface extends LoggerInterface
{
    public function setItemIndex($itemIndex): void;

    public function logException(\Throwable $e): void;

    public function getRecords(): array;
}
