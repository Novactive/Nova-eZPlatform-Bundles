<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Search;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;

/**
 * @template TSearchHitValueObject of \Ibexa\Contracts\Core\Repository\Values\ValueObject
 * @template TExpandedSearchHitValueObject of \Ibexa\Contracts\Core\Repository\Values\ValueObject
 *
 * @extends SearchResult<TSearchHitValueObject>
 */
class ExtendedSearchResult extends SearchResult
{
    /**
     * @var SearchResult<TExpandedSearchHitValueObject>[]
     */
    protected array $expanded = [];

    /**
     * @return SearchResult<TExpandedSearchHitValueObject>[]
     */
    public function getExpandedResults(): array
    {
        return $this->expanded;
    }
}
