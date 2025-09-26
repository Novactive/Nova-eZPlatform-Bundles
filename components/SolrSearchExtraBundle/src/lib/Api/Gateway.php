<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Api;

use Exception;
use Ibexa\Solr\Gateway\DistributionStrategy;
use Ibexa\Solr\Gateway\Endpoint;
use Ibexa\Solr\Gateway\EndpointRegistry;
use Ibexa\Solr\Gateway\EndpointResolver;
use Ibexa\Solr\Gateway\HttpClient;
use Ibexa\Solr\Gateway\Message;
use Ibexa\Solr\Gateway\Native;
use Ibexa\Solr\Gateway\UpdateSerializerInterface;
use Ibexa\Solr\Query\QueryConverter;
use Novactive\EzSolrSearchExtra\Query\DocumentQuery;
use stdClass;

class Gateway extends Native
{
    protected QueryConverter $queryConverter;

    public function __construct(
        QueryConverter $queryConverter,
        HttpClient $client,
        EndpointResolver $endpointResolver,
        EndpointRegistry $endpointRegistry,
        QueryConverter $contentQueryConverter,
        QueryConverter $locationQueryConverter,
        UpdateSerializerInterface $updateSerializer,
        DistributionStrategy $distributionStrategy
    ) {
        $this->queryConverter = $queryConverter;
        parent::__construct(
            $client,
            $endpointResolver,
            $endpointRegistry,
            $contentQueryConverter,
            $locationQueryConverter,
            $updateSerializer,
            $distributionStrategy
        );
    }

    public function findDocument(DocumentQuery $query, array $languageSettings = [])
    {
        $parameters = $this->queryConverter->convert($query, $languageSettings);

        return $this->internalFind($parameters, $languageSettings);
    }

    public function rawSearch(array $parameters, array $languageSettings = [])
    {
        return $this->internalFind($parameters, $languageSettings);
    }

    /**
     * @param string[] $ids
     */
    public function deleteDocuments(array $ids): void
    {
        $ids = array_map(function ($value) {
            return preg_replace('([^A-Za-z0-9/*]+)', '', $value);
        }, $ids);

        $query = 'id:('.implode(' OR ', $ids).')';
        $this->deleteByQuery($query);
    }

    public function purgeDocumentsFromIndex(): void
    {
        $this->deleteByQuery('document_type_id:"document"');
    }

    /**
     * @throws \Exception
     */
    public function reload(): void
    {
        $endpoint = $this->getAdminEndpoint();

        $this->request(
            'GET',
            '&action=RELOAD',
            null,
            $endpoint
        );
    }

    public function getAdminEndpoint(): AdminEndpoint
    {
        $endpoint = $this->getEndpoint();

        return new AdminEndpoint(
            [
                'scheme' => $endpoint->scheme,
                'user' => $endpoint->user,
                'pass' => $endpoint->pass,
                'host' => $endpoint->host,
                'port' => $endpoint->port,
                'path' => $endpoint->path,
                'core' => $endpoint->core,
            ]
        );
    }

    public function getEndpoint(): Endpoint
    {
        return $this->endpointRegistry->getEndpoint(
            $this->endpointResolver->getEntryEndpoint()
        );
    }

    /**
     * @throws \Exception
     */
    public function request(
        string $method,
        string $path,
        ?Message $message = null,
        ?Endpoint $endpoint = null
    ): ?stdClass {
        if (null === $endpoint) {
            $endpoint = $this->getEndpoint();
        }
        $response = $this->client->request(
            $method,
            $endpoint,
            $path,
            $message
        );
        $result = json_decode($response->body);
        if ($result && 500 === $result->responseHeader->status) {
            throw new Exception($result->error->msg);
        }

        return $result;
    }
}
