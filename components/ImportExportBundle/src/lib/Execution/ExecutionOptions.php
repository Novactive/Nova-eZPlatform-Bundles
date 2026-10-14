<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Execution;

use AlmaviaCX\Bundle\IbexaImportExport\Processor\ProcessorOptions;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderOptions;

class ExecutionOptions
{
    /**
     * @param array<string, ProcessorOptions> $processorsOptions
     */
    public function __construct(
        public ?ReaderOptions $readerOptions = null,
        public array $processorsOptions = []
    ) {
    }

    public function getReaderOptions(): ?ReaderOptions
    {
        return $this->readerOptions;
    }

    /**
     * @return array<string, ProcessorOptions>|null
     */
    public function getProcessorsOptions(): ?array
    {
        return $this->processorsOptions;
    }

    public function getProcessorOptions(string $id): ?ProcessorOptions
    {
        return $this->processorsOptions[$id] ?? null;
    }

    public function merge(ExecutionOptions $overrideOptions): ExecutionOptions
    {
        if (null !== $overrideOptions->getReaderOptions()) {
            $this->readerOptions = $this->readerOptions ?
                $this->readerOptions->merge($overrideOptions->getReaderOptions()) :
                $overrideOptions->getReaderOptions();
        }

        foreach ($overrideOptions->getProcessorsOptions() as $processorId => $overrideProcessorsOption) {
            $this->processorsOptions[$processorId] = isset($this->processorsOptions[$processorId]) ?
                $this->processorsOptions[$processorId]->merge($overrideProcessorsOption) :
                $overrideProcessorsOption;
        }

        return $this;
    }
}
