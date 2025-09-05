<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Workflow;

use AlmaviaCX\Bundle\IbexaImportExport\Monolog\WorkflowLoggerInterface;

interface WorkflowInterface
{
    public function __invoke(int $batchLimit = -1): void;

    public function addEventListener(string $eventName, callable $listener, int $priority = 0);

    public function setConfiguration(WorkflowExecutionConfiguration $configuration): void;

    public function getDefaultConfig(): WorkflowConfiguration;

    public function getStartTime(): \DateTimeImmutable;

    public function getEndTime(): \DateTimeImmutable;

    public function getWriterResults(): array;

    public function setWriterResults(array $writerResults): void;

    public function getOffset(): int;

    public function setOffset(int $offset): void;

    public function getTotalItemsCount(): int;

    public function setTotalItemsCount(?int $totalItemsCount): void;

    public function setLogger(WorkflowLoggerInterface $logger): void;

    public function clean(): void;

    public function setDebug(bool $debug): void;
}
