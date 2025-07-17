<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Repository;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Novactive\EzSolrSearchExtra\Query\DocumentQuery;

interface DocumentSearchServiceInterface
{
    public function findDocument(
        DocumentQuery $query,
        array $languageFilter = [],
        bool $filterOnUserPermissions = true
    ): SearchResult;

    public function purgeDocumentsFromIndex(): void;
}
