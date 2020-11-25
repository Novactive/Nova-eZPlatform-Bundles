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

namespace Novactive\Bundle\eZAlgoliaSearchEngine\Core;

use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\Search;

final class ClientService
{
    /**
     * @var Search
     */
    private $contentSearchService;

    /**
     * @var Search
     */
    private $locationSearchService;

    public function __construct(Search $contentSearch, Search $locationSearch)
    {
        $this->contentSearchService = $contentSearch;
        $this->locationSearchService = $locationSearch;
    }

    public function contentSearch(
        string $languageCode,
        ?string $replaicaName = null,
        string $query = '',
        array $requestOptions = [],
        array $facetBuilders = []
    ): SearchResult {
        $requestOptions['filters'] .= ' AND doc_type_s:content';

        return $this->contentSearchService->getExtractedSearchResult(
            $languageCode,
            $replaicaName,
            $query,
            $requestOptions,
            $facetBuilders
        );
    }

    public function locationSearch(
        string $languageCode,
        ?string $replaicaName = null,
        string $query = '',
        array $requestOptions = [],
        array $facetBuilders = []
    ): SearchResult {
        $requestOptions['filters'] .= ' AND doc_type_s:location';

        return $this->locationSearchService->getExtractedSearchResult(
            $languageCode,
            $replaicaName,
            $query,
            $requestOptions,
            $facetBuilders
        );
    }

    public function rawSearch(
        string $languageCode,
        ?string $replaicaName = null,
        string $query = '',
        array $requestOptions = []
    ): array {
        return $this->contentSearchService->sendClientRequest($languageCode, $replaicaName, $query, $requestOptions);
    }
}
