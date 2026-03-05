<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Api;

use Exception;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Solr\Gateway\DistributionStrategy;
use Ibexa\Solr\Gateway\DistributionStrategy\CloudDistributionStrategy;
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
    protected ConfigResolverInterface $configResolver;

    public function __construct(
        QueryConverter $queryConverter,
        HttpClient $client,
        EndpointResolver $endpointResolver,
        EndpointRegistry $endpointRegistry,
        QueryConverter $contentQueryConverter,
        QueryConverter $locationQueryConverter,
        UpdateSerializerInterface $updateSerializer,
        DistributionStrategy $distributionStrategy,
        ConfigResolverInterface $configResolver
    ) {
        $this->queryConverter = $queryConverter;
        $this->configResolver = $configResolver;

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

    public function getDistributionStrategyIdentifier(): string
    {
        $distributionStrategyIdentifier = $this->configResolver->getParameter(
            'distribution_strategy_identifier',
            'nova_solr_extra'
        );
        if (null === $distributionStrategyIdentifier) {
            $distributionStrategyIdentifier = $this->distributionStrategy instanceof CloudDistributionStrategy ?
                'cloud' :
                'standalone';
        }

        return $distributionStrategyIdentifier;
    }

    public function getAdminEndpoint(): AdminEndpoint
    {
        $endpoint = $this->getEndpoint();

        $distributionStrategyIdentifier = $this->getDistributionStrategyIdentifier();

        return new AdminEndpoint(
            [
                'scheme' => $endpoint->scheme,
                'user' => $endpoint->user,
                'pass' => $endpoint->pass,
                'host' => $endpoint->host,
                'port' => $endpoint->port,
                'path' => $endpoint->path,
                'core' => $endpoint->core,
                'distributionStrategyIdentifier' => $distributionStrategyIdentifier,
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
