<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\ApiLoader;

use Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider;
use Ibexa\Solr\Gateway\EndpointRegistry;
use Ibexa\Solr\Gateway\HttpClient;
use Novactive\EzSolrSearchExtra\Api\Gateway;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class GatewayFactory implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    
    /**
     * @param $defaultConnection
     */
    public function __construct(
        protected RepositoryConfigurationProvider $repositoryConfigurationProvider,
        protected $defaultConnection
    ) {
    }

    public function buildGateway(HttpClient $client, EndpointRegistry $endpointRegistry): Gateway
    {
        $repositoryConfig = $this->repositoryConfigurationProvider->getRepositoryConfig();

        $connection = $this->defaultConnection;
        if (isset($repositoryConfig['search']['connection'])) {
            $connection = $repositoryConfig['search']['connection'];
        }

        return new Gateway(
            $client,
            $this->container->get("nova.solr.connection.$connection.endpoint_resolver_id"),
            $endpointRegistry
        );
    }
}
