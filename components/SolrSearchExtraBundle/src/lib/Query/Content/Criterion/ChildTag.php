<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Content\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

class ChildTag extends Criterion
{
    public function __construct(
        public string $ofParameter,
        public Criterion $criterion
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecifications(): array
    {
        return [];
    }
}
