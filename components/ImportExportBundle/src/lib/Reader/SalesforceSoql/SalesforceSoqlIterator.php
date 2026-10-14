<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\SalesforceSoql;

use AlmaviaCX\Bundle\IbexaImportExport\Accessor\ArrayAccessor;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderIteratorInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Salesforce\SalesforceApiClient;
use AlmaviaCX\Bundle\IbexaImportExport\Salesforce\SalesforceApiCredentials;
use AlmaviaCX\Bundle\IbexaImportExport\Salesforce\SalesforceApiException;
use Doctrine\Common\Collections\ArrayCollection;
use SeekableIterator;

/**
 * @implements ReaderIteratorInterface<int, ArrayAccessor>
 * @implements SeekableIterator<int, ArrayAccessor>
 */
class SalesforceSoqlIterator implements ReaderIteratorInterface, \SeekableIterator
{
    protected int $position = 0;

    protected ?SoqlResultsIterator $innerIterator = null;

    /**
     * @param ArrayCollection<string, mixed> $cache
     */
    public function __construct(
        protected SalesforceApiClient $apiClient,
        protected SalesforceApiCredentials $credentials,
        protected string $domain,
        protected string $version,
        protected string $queryString,
        protected string $countQueryString,
        protected ArrayCollection $cache,
    ) {
    }

    protected function fetch(): SoqlResultsIterator
    {
        if (!$this->cache->containsKey('results_iterator')) {
            $iterator = $this->getFirstPageIterator();
            $this->cache->set('results_iterator', $iterator);
        }
        /** @var SoqlResultsIterator $iterator */
        $iterator = $this->cache->get('results_iterator');

        $relativePosition = $iterator->getBatchSize() > 0 ?
            $this->position % $iterator->getBatchSize() :
            $this->position;
        $queryOffset = $this->position - $relativePosition;

        if ($queryOffset !== $iterator->getQueryOffset()) {
            $results = $this->getResults($iterator, $queryOffset);
            $iterator = new SoqlResultsIterator(
                $results['records'] ?? [],
                $iterator->getQueryId(),
                $iterator->getBatchSize(),
                $queryOffset,
                $results['nextRecordsUrl'] ?? null
            );
            $this->cache->set('results_iterator', $iterator);
        }

        $iterator->seek($relativePosition);

        return $iterator;
    }

    protected function initialize(): void
    {
        $this->innerIterator = $this->fetch();
    }

    protected function isInitialized(): bool
    {
        return isset($this->innerIterator);
    }

    public function count(): int
    {
        $response = $this->executeQuery($this->countQueryString);

        return $response['totalSize'] ?? 0;
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws SalesforceApiException
     * @throws \JsonException
     *
     * @return array<mixed, mixed>
     */
    protected function executeQuery(string $queryString): array
    {
        $path = sprintf(
            '/query?%s',
            http_build_query(['q' => $queryString])
        );

        return $this->request($path);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws SalesforceApiException
     * @throws \JsonException
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     *
     * @return array<mixed, mixed>
     */
    protected function request(string $path): array
    {
        return ($this->apiClient)(
            $this->domain,
            $this->version,
            $path,
            'GET',
            $this->credentials
        );
    }

    /**
     * @return ArrayAccessor<string, mixed>
     */
    public function current(): ArrayAccessor
    {
        if (!$this->isInitialized()) {
            $this->initialize();
        }

        return new ArrayAccessor(
            $this->innerIterator->current()
        );
    }

    public function next(): void
    {
        if (!$this->isInitialized()) {
            $this->initialize();
        }
        ++$this->position;
        $this->innerIterator->next();
        if (!$this->innerIterator->valid() && null !== $this->innerIterator->getNextRecordsUrl()) {
            $this->innerIterator = $this->fetch();
        }
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        if (!$this->isInitialized()) {
            $this->initialize();
        }

        return $this->innerIterator->valid();
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function seek(int $offset): void
    {
        $this->position = $offset;
        $this->innerIterator = $this->fetch();
    }

    /**
     * @return array<mixed, mixed>
     */
    protected function getResults(SoqlResultsIterator $iterator, int $queryOffset): array
    {
        try {
            return $this->request(
                sprintf(
                    '/query/%s-%d',
                    $iterator->getQueryId(),
                    $queryOffset
                )
            );
        } catch (SalesforceApiException $e) {
            if ('INVALID_QUERY_LOCATOR' === $e->getErrorCode()) {
                $firstPageIterator = $this->getFirstPageIterator();

                return $this->request(
                    sprintf(
                        '/query/%s-%d',
                        $firstPageIterator->getQueryId(),
                        $queryOffset
                    )
                );
            } else {
                throw $e;
            }
        }
    }

    protected function getFirstPageIterator(): SoqlResultsIterator
    {
        $firstPageQueryResults = $this->executeQuery($this->queryString);
        $nextRecordsUrl = $firstPageQueryResults['nextRecordsUrl'] ?? null;
        $queryId = null;
        $batchSize = null;
        if (
            $nextRecordsUrl
            && preg_match(
                '#/services/data/v([\d.]+)/query/([\w]+)-([\d]+)$#',
                $nextRecordsUrl,
                $matches
            )
        ) {
            $queryId = $matches[2];
            $batchSize = (int) $matches[3];
        }

        return new SoqlResultsIterator(
            $firstPageQueryResults['records'] ?? [],
            $queryId,
            $batchSize,
            0,
            $nextRecordsUrl
        );
    }
}
