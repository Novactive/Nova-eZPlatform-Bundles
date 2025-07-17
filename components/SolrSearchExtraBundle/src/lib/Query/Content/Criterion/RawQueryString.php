<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Content\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

class RawQueryString extends Criterion
{
    public string $queryString;

    public function __construct(
        string $queryString
    ) {
        $this->queryString = $queryString;
    }

    public function getSpecifications(): array
    {
        return [];
    }
}
