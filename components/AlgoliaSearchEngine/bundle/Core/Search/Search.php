<?php

/**
 * Nova eZ Algolia Search Engine.
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @licence   "SEE FULL LICENSE OPTIONS IN LICENSE.md"
 *            Nova eZ Algolia Search Engine is tri-licensed, meaning you must choose one of three licenses to use:
 *                - Commercial License: a paid license, meant for commercial use. The default option for most users.
 *                - Creative Commons Non-Commercial No-Derivatives: meant for trial and non-commercial use.
 *                - GPLv3 License: meant for open-source projects.
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZAlgoliaSearchEngine\Core\Search;

use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use Novactive\Bundle\eZAlgoliaSearchEngine\Core\ClientService;

class Search
{
    /**
     * @var ClientService
     */
    private $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function findContents(Query $query, array $facetBuilders = []): SearchResult
    {
        return $this->clientService->contentSearch(
            $query->getLanguage(),
            $query->getReplica(),
            $query->getTerm(),
            $this->getRequestOptions($query),
            $facetBuilders
        );
    }

    public function findLocations(Query $query, array $facetBuilders = []): SearchResult
    {
        return $this->clientService->locationSearch(
            $query->getLanguage(),
            $query->getReplica(),
            $query->getTerm(),
            $this->getRequestOptions($query),
            $facetBuilders
        );
    }

    public function find(Query $query): Result
    {
        return Result::createFromResponse(
            $this->clientService->rawSearch(
                $query->getLanguage(),
                $query->getReplica(),
                $query->getTerm(),
                $this->getRequestOptions($query)
            )
        );
    }

    private function getRequestOptions(Query $query): array
    {
        $requestOptions = [
            'filters' => $query->getFiltersString(),
            'attributesToHighlight' => [],
            'page' => $query->getPage(),
            'hitsPerPage' => $query->getHitsPerPage(),
            'facets' => $query->getFacets(),
        ];

        return array_merge($requestOptions, $query->getRequestOptions());
    }
}
