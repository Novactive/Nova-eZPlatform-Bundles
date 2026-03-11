<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Search;

use Ibexa\Contracts\Core\Container\ApiLoader\RepositoryConfigurationProviderInterface;
use Ibexa\Solr\CoreFilter;
use Ibexa\Solr\CoreFilter\CoreFilterRegistry;
use Novactive\EzSolrSearchExtra\Api\Gateway;
use Novactive\EzSolrSearchExtra\Api\GatewayRegistry;
use Novactive\EzSolrSearchExtra\ResultExtractor\DocumentResultExtractor;

class ExtendedSearchHandlerFactory
{
    protected CoreFilter $coreFilter;
    protected Gateway $gateway;

    public function __construct(
        protected RepositoryConfigurationProviderInterface $repositoryConfigurationProvider,
        protected string $defaultConnection,
        protected GatewayRegistry $gatewayRegistry,
        protected CoreFilterRegistry $coreFilterRegistry,
        protected DocumentResultExtractor $resultExtractor
    ) {
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
