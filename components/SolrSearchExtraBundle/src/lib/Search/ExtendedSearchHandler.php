<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Search;

use Exception;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\Core\Search\Document;
use Ibexa\Solr\CoreFilter;
use Ibexa\Solr\Gateway\Endpoint;
use Ibexa\Solr\Gateway\Message;
use Novactive\EzSolrSearchExtra\Api\Gateway;
use Novactive\EzSolrSearchExtra\Query\DocumentQuery;
use Novactive\EzSolrSearchExtra\ResultExtractor\DocumentResultExtractor;
use Novactive\EzSolrSearchExtra\Values\DocumentHit;
use stdClass;

class ExtendedSearchHandler
{
    protected CoreFilter $coreFilter;
    protected Gateway $gateway;
    protected DocumentResultExtractor $resultExtractor;

    public function __construct(
        CoreFilter $coreFilter,
        Gateway $gateway,
        DocumentResultExtractor $resultExtractor
    ) {
        $this->resultExtractor = $resultExtractor;
        $this->gateway = $gateway;
        $this->coreFilter = $coreFilter;
    }

    /**
     * @param array{languages?: string[], languageCode?: string, useAlwaysAvailable?: bool} $languageFilter
     *
     * @return ExtendedSearchResult<DocumentHit, ValueObject>
     */
    public function findDocument(DocumentQuery $query, array $languageFilter = []): SearchResult
    {
        $query = clone $query;
        $query->filter = $query->filter ?: new Criterion\MatchAll();
        $query->query = $query->query ?: new Criterion\MatchAll();

        $this->coreFilter->apply(
            $query,
            $languageFilter,
            $query->documentType
        );

        return $this->resultExtractor->extract(
            $this->gateway->findDocument($query, $languageFilter),
            $query->aggregations,
            $languageFilter,
            $query->spellcheck
        );
    }

    /**
     * @param array<string, mixed>                                                          $parameters
     * @param array{languages?: string[], languageCode?: string, useAlwaysAvailable?: bool} $languageSettings
     */
    public function rawSearch(array $parameters, array $languageSettings = []): mixed
    {
        return $this->gateway->rawSearch($parameters, $languageSettings);
    }

    /**
     * @param string[] $ids
     */
    public function deleteDocuments(array $ids): void
    {
        $this->gateway->deleteDocuments($ids);
    }

    public function purgeDocumentsFromIndex(): void
    {
        $this->gateway->purgeDocumentsFromIndex();
    }

    public function indexDocument(Document $document): void
    {
        $this->gateway->bulkIndexDocuments([[$document]]);
    }

    /**
     * @param Document[] $documents
     */
    public function bulkIndexDocuments(array $documents): void
    {
        $this->gateway->bulkIndexDocuments([$documents]);
    }

    /**
     * @throws Exception
     */
    public function request(
        string $method,
        string $path,
        ?Message $message = null,
        ?Endpoint $endpoint = null
    ): ?stdClass {
        return $this->gateway->request($method, $path, $message, $endpoint);
    }

    /**
     * @throws Exception
     */
    public function reload(): void
    {
        $endpoint = $this->gateway->getAdminEndpoint();
        $this->gateway->request(
            'GET',
            '&action=RELOAD',
            null,
            $endpoint
        );
    }
}
