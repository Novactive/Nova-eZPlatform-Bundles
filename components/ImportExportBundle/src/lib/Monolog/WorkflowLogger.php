<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Monolog;

use AlmaviaCX\Bundle\IbexaImportExport\Monolog\Handler\WorkflowHandler;
use Monolog\DateTimeImmutable;
use Monolog\Logger;
use Throwable;

class WorkflowLogger extends Logger implements WorkflowLoggerInterface
{
    use WorkflowLoggerTrait;

    protected WorkflowHandler $logHandler;

    public function __construct()
    {
        $this->logHandler = new WorkflowHandler();
        parent::__construct('importexport.workflow', [$this->logHandler]);
    }

    public function addRecord(
        int $level,
        string $message,
        array $context = [],
        DateTimeImmutable $datetime = null
    ): bool {
        if ($this->itemIndex) {
            $context['item_index'] = $this->itemIndex;
        }

        return parent::addRecord($level, $message, $context, $datetime);
    }

    public function logException(Throwable $e): void
    {
        $this->error($e->getMessage(), ['exception' => $e->getTraceAsString()]);
    }

    public function getRecords(): array
    {
        return $this->logHandler->getRecords();
    }

    public function clearRecords(): void
    {
        $this->logHandler->clear();
    }
}
