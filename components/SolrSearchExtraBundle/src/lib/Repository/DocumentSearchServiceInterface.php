<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Repository;

use Novactive\EzSolrSearchExtra\Query\DocumentQuery;
use Novactive\EzSolrSearchExtra\Search\ExtendedSearchResult;

interface DocumentSearchServiceInterface
{
    public function findDocument(
        DocumentQuery $query,
        array $languageFilter = [],
        bool $filterOnUserPermissions = true
    ): ExtendedSearchResult;

    public function purgeDocumentsFromIndex(): void;
}
