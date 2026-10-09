<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\SalesforceSoql;

use ArrayIterator;

/**
 * @extends ArrayIterator<int, array<string, mixed>>
 */
class SoqlResultsIterator extends ArrayIterator
{
    /**
     * @param array<int, array<string, mixed>> $array
     */
    public function __construct(
        array $array,
        protected ?string $queryId,
        protected ?int $batchSize,
        protected int $queryOffset,
        protected ?string $nextRecordsUrl,
    ) {
        parent::__construct($array);
    }

    public function getBatchSize(): ?int
    {
        return $this->batchSize;
    }

    public function getQueryOffset(): int
    {
        return $this->queryOffset;
    }

    public function getQueryId(): ?string
    {
        return $this->queryId;
    }

    public function getNextRecordsUrl(): ?string
    {
        return $this->nextRecordsUrl;
    }
}
