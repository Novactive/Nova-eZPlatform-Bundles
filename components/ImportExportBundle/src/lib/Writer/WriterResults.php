<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer;

class WriterResults
{
    protected string $writerType;
    /** @var mixed[] */
    protected array $results;

    /**
     * @param mixed[] $results
     */
    public function __construct(string $writerType, array $results)
    {
        $this->writerType = $writerType;
        $this->results = $results;
    }

    public function getWriterType(): string
    {
        return $this->writerType;
    }

    /**
     * @return mixed[]
     */
    public function getResults(): array
    {
        return $this->results;
    }

    public function setResult(string $key, $value): void
    {
        $this->results[$key] = $value;
    }

    public function getResult(string $key, $default = null)
    {
        return $this->results[$key] ?? $default;
    }
}
