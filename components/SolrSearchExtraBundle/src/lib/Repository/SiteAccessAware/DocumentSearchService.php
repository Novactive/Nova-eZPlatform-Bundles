<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Repository\SiteAccessAware;

use Ibexa\Contracts\Core\Repository\LanguageResolver;
use Novactive\EzSolrSearchExtra\Query\DocumentQuery;
use Novactive\EzSolrSearchExtra\Repository\DocumentSearchService as NativeDocumentSearchService;
use Novactive\EzSolrSearchExtra\Repository\DocumentSearchServiceInterface;
use Novactive\EzSolrSearchExtra\Search\ExtendedSearchResult;

class DocumentSearchService implements DocumentSearchServiceInterface
{
    protected NativeDocumentSearchService $documentSearchService;
    protected LanguageResolver $languageResolver;

    public function __construct(
        NativeDocumentSearchService $documentSearchService,
        LanguageResolver $languageResolver
    ) {
        $this->languageResolver = $languageResolver;
        $this->documentSearchService = $documentSearchService;
    }

    public function findDocument(
        DocumentQuery $query,
        array $languageFilter = [],
        bool $filterOnUserPermissions = true
    ): ExtendedSearchResult {
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
