<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Content\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

class ParentTag extends Criterion
{
    public Criterion $criterion;
    public string $whichParameter;

    public function __construct(
        string $whichParameter,
        Criterion $criterion
    ) {
        $this->whichParameter = $whichParameter;
        $this->criterion = $criterion;
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecifications(): array
    {
        return [];
    }
}
