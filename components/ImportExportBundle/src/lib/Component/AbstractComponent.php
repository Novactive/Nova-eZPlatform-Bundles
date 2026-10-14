<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Component;

use AlmaviaCX\Bundle\IbexaImportExport\Monolog\WorkflowLoggerInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Reference\ReferenceBag;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowState;
use InvalidArgumentException;

/**
 * @template TComponentOptions of ComponentOptions
 * @implements ComponentInterface<TComponentOptions>
 */
abstract class AbstractComponent implements ComponentInterface
{
    protected WorkflowLoggerInterface $logger;
    protected WorkflowState $workflowState;

    /**
     * @var TComponentOptions
     */
    protected $options;

    public static function getOptionsFormType(): ?string
    {
        return null;
    }

    public static function getOptionsType(): string
    {
        return ComponentOptions::class;
    }

    public function setOptions($options): void
    {
        $requiredOptionType = static::getOptionsType();
        if (!$options instanceof $requiredOptionType) {
            throw new InvalidArgumentException('Options must be an instance of '.$requiredOptionType);
        }
        $this->options = $options;
    }

    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return mixed|null
     */
    public function getOption(string $name, mixed $default = null): mixed
    {
        return $this->options->{$name} ?? $default;
    }

    public function clean(): void
    {
    }

    public function prepare(): void
    {
    }

    public function finish(): void
    {
    }

    public function setLogger(WorkflowLoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function setState(WorkflowState $state): void
    {
        $this->workflowState = $state;
    }

    protected function getReferenceBag(): ReferenceBag
    {
        return $this->workflowState->getReferenceBag();
    }
}
