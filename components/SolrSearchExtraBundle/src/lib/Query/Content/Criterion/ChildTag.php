<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Content\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

class ChildTag extends Criterion
{
    public string $ofParameter;
    public Criterion $criterion;
    public ?string $tag = null;

    public function __construct(
        string $ofParameter,
        Criterion $criterion,
        ?string $tag = null
    ) {
        $this->criterion = $criterion;
        $this->ofParameter = $ofParameter;
        $this->tag = $tag;
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecifications(): array
    {
        return [];
    }
}
