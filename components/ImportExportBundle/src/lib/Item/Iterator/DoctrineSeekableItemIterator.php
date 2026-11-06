<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\Iterator;

use Doctrine\DBAL\Connection;

class DoctrineSeekableItemIterator extends PaginatedQueryIterator
{
    public function __construct(
        protected Connection $connection,
        string $queryString,
        protected string $countQueryString,
        int $batchSize = self::DEFAULT_BATCH_SIZE
    ) {
        parent::__construct($queryString, $batchSize);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    protected function executeQuery(string $queryString): array
    {
        return $this->connection->executeQuery($queryString)->fetchAllAssociative();
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function count(): int
    {
        return $this->connection->executeQuery($this->countQueryString)->fetchOne();
    }
}
