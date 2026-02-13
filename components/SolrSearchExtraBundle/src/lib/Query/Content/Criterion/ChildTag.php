<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Content\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

class ChildTag extends Criterion
{
    public string $ofParameter;
    public Criterion $criterion;

    public function __construct(
        string $ofParameter,
        Criterion $criterion
    ) {
        $this->criterion = $criterion;
        $this->ofParameter = $ofParameter;
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecifications(): array
    {
        return [];
    }
}
