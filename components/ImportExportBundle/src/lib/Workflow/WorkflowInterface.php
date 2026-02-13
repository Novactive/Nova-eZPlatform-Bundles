<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Workflow;

use AlmaviaCX\Bundle\IbexaImportExport\Monolog\WorkflowLoggerInterface;

interface WorkflowInterface
{
    public function __invoke(int $batchLimit = -1): void;

    public function addEventListener(string $eventName, callable $listener, int $priority = 0): void;

    public function setConfiguration(WorkflowExecutionConfiguration $configuration): void;

    public function getDefaultConfig(): WorkflowConfiguration;

    public function setState(WorkflowState $state): void;

    public function getState(): WorkflowState;

    public function setLogger(WorkflowLoggerInterface $logger): void;

    public function getLogger(): ?WorkflowLoggerInterface;

    public function clean(): void;

    public function setDebug(bool $debug): void;

    public function isDebug(): bool;
}
