<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Content\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

class ParentTag extends Criterion
{
    public Criterion $criterion;
    public string $whichParameter;
    public ?string $tag = null;

    public function __construct(
        string $whichParameter,
        Criterion $criterion,
        ?string $tag = null
    ) {
        $this->whichParameter = $whichParameter;
        $this->criterion = $criterion;
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
