<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Content\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

class RawQueryString extends Criterion
{
    public function __construct(
        public string $queryString
    ) {
    }

    public function getSpecifications(): array
    {
        return [];
    }
}
