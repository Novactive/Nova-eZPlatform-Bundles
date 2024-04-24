<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Workflow;

use AlmaviaCX\Bundle\IbexaImportExport\Monolog\WorkflowLoggerInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Result\Result;
use DateTimeImmutable;

interface WorkflowInterface
{
    public function __invoke(): Result;

    public function addEventListener(string $eventName, callable $listener, int $priority = 0);

    public function setConfiguration(WorkflowExecutionConfiguration $configuration): void;

    public static function getDefaultConfig(): WorkflowConfiguration;

    public static function getConfigurationFormType(): ?string;

    public function getStartTime(): DateTimeImmutable;

    public function getEndTime(): DateTimeImmutable;

    public function getWriterResults(): array;

    public function getProgress(): float;

    public function getTotalItemsCount(): int;

    public function setLogger(WorkflowLoggerInterface $logger): void;

    public function clean(): void;
}
