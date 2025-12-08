<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Search;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;

class ExtendedSearchResult extends SearchResult
{
    /**
     * @var SearchResult[]
     */
    protected array $expanded = [];

    /**
     * @return SearchResult[]
     */
    public function getExpandedResults(): array
    {
        return $this->expanded;
    }
}
