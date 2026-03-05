<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Search;

use Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider;
use Ibexa\Solr\CoreFilter;
use Ibexa\Solr\CoreFilter\CoreFilterRegistry;
use Novactive\EzSolrSearchExtra\Api\Gateway;
use Novactive\EzSolrSearchExtra\Api\GatewayRegistry;
use Novactive\EzSolrSearchExtra\ResultExtractor\DocumentResultExtractor;

class ExtendedSearchHandlerFactory
{
    protected CoreFilter $coreFilter;
    protected Gateway $gateway;
    protected DocumentResultExtractor $resultExtractor;
    protected GatewayRegistry $gatewayRegistry;
    protected CoreFilterRegistry $coreFilterRegistry;
    protected RepositoryConfigurationProvider $repositoryConfigurationProvider;
    protected $defaultConnection;

    public function __construct(
        RepositoryConfigurationProvider $repositoryConfigurationProvider,
        $defaultConnection,
        GatewayRegistry $gatewayRegistry,
        CoreFilterRegistry $coreFilterRegistry,
        DocumentResultExtractor $resultExtractor
    ) {
        $this->defaultConnection = $defaultConnection;
        $this->repositoryConfigurationProvider = $repositoryConfigurationProvider;
        $this->coreFilterRegistry = $coreFilterRegistry;
        $this->gatewayRegistry = $gatewayRegistry;
        $this->resultExtractor = $resultExtractor;
    }

    public function build(): ExtendedSearchHandler
    {
        $repositoryConfig = $this->repositoryConfigurationProvider->getRepositoryConfig();

        $connection = $repositoryConfig['search']['connection'] ?? $this->defaultConnection;

        $gateway = $this->gatewayRegistry->getGateway($connection);
        $coreFilter = $this->coreFilterRegistry->getCoreFilter($connection);

        return new ExtendedSearchHandler(
            $coreFilter,
            $gateway,
            $this->resultExtractor
        );
    }
}
