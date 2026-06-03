<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Repository;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Novactive\EzSolrSearchExtra\Query\DocumentQuery;
use Novactive\EzSolrSearchExtra\Search\ExtendedSearchResult;
use Novactive\EzSolrSearchExtra\Values\DocumentHit;

interface DocumentSearchServiceInterface
{
    /**
     * @param array{languages?: string[], languageCode?: string, useAlwaysAvailable?: bool} $languageFilter
     *
     * @return ExtendedSearchResult<DocumentHit, ValueObject>
     */
    public function findDocument(
        DocumentQuery $query,
        array $languageFilter = [],
        bool $filterOnUserPermissions = true
    ): ExtendedSearchResult;

    public function purgeDocumentsFromIndex(): void;
}
