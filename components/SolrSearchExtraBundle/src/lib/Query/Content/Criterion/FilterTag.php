<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Content\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

class FilterTag extends Criterion
{
    /**
     * FilterTag constructor.
     */
    public function __construct(public string $tag, public Criterion $criterion)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecifications(): array
    {
        return [];
    }
}
