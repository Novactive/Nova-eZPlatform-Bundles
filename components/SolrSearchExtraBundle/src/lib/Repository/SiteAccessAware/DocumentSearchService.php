<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Repository\SiteAccessAware;

use Ibexa\Contracts\Core\Repository\LanguageResolver;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Novactive\EzSolrSearchExtra\Query\DocumentQuery;
use Novactive\EzSolrSearchExtra\Repository\DocumentSearchService as NativeDocumentSearchService;
use Novactive\EzSolrSearchExtra\Repository\DocumentSearchServiceInterface;

class DocumentSearchService implements DocumentSearchServiceInterface
{
    public function __construct(
        protected NativeDocumentSearchService $documentSearchService,
        protected LanguageResolver $languageResolver,
    ) {
    }

    public function findDocument(
        DocumentQuery $query,
        array $languageFilter = [],
        bool $filterOnUserPermissions = true,
    ): SearchResult {
        $languageFilter['languages'] = $this->languageResolver->getPrioritizedLanguages(
            $languageFilter['languages'] ?? null
        );

        $languageFilter['useAlwaysAvailable'] = $this->languageResolver->getUseAlwaysAvailable(
            $languageFilter['useAlwaysAvailable'] ?? null
        );

        return $this->documentSearchService->findDocument(
            $query,
            $languageFilter,
            $filterOnUserPermissions
        );
    }

    public function purgeDocumentsFromIndex(): void
    {
        $this->documentSearchService->purgeDocumentsFromIndex();
    }
}
