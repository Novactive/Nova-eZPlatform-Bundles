<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer;

class WriterResults
{
    /**
     * @param array<string, mixed> $results
     */
    public function __construct(
        protected string $writerType,
        protected array $results
    ) {
    }

    public function getWriterType(): string
    {
        return $this->writerType;
    }

    /**
     * @return array<string, mixed>
     */
    public function getResults(): array
    {
        return $this->results;
    }

    public function setResult(string $key, mixed $value): void
    {
        $this->results[$key] = $value;
    }

    /**
     * @param mixed $default
     *
     * @return mixed|null
     */
    public function getResult(string $key, mixed $default = null): mixed
    {
        return $this->results[$key] ?? $default;
    }
}
