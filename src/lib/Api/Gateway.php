<?php

/**
 * NovaeZSolrSearchExtraBundle.
 *
 * @package   NovaeZSolrSearchExtraBundle
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZSolrSearchExtraBundle/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Api;

use Exception;
use EzSystems\EzPlatformSolrSearchEngine\Gateway\Endpoint;
use EzSystems\EzPlatformSolrSearchEngine\Gateway\EndpointRegistry;
use EzSystems\EzPlatformSolrSearchEngine\Gateway\EndpointResolver;
use EzSystems\EzPlatformSolrSearchEngine\Gateway\HttpClient;
use EzSystems\EzPlatformSolrSearchEngine\Gateway\Message;
use stdClass;

class Gateway
{
    /**
     * HTTP client to communicate with Solr server.
     *
     * @var HttpClient
     */
    protected $client;

    /**
     * @var EndpointResolver
     */
    protected $endpointResolver;

    /**
     * Endpoint registry service.
     *
     * @var EndpointRegistry
     */
    protected $endpointRegistry;

    /**
     * Gateway constructor.
     */
    public function __construct(
        HttpClient $client,
        EndpointResolver $endpointResolver,
        EndpointRegistry $endpointRegistry
    ) {
        $this->client           = $client;
        $this->endpointResolver = $endpointResolver;
        $this->endpointRegistry = $endpointRegistry;
    }

    public function reload()
    {
        $endpoint = $this->getAdminEndpoint();

        $response = $this->request(
            'GET',
            sprintf('&action=RELOAD'),
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
                'user'   => $endpoint->user,
                'pass'   => $endpoint->pass,
                'host'   => $endpoint->host,
                'port'   => $endpoint->port,
                'path'   => $endpoint->path,
                'core'   => $endpoint->core,
            ]
        );
    }

    public function getEndpoint(): Endpoint
    {
        return $this->endpointRegistry->getEndpoint(
            $this->endpointResolver->getEntryEndpoint()
        );
    }

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
