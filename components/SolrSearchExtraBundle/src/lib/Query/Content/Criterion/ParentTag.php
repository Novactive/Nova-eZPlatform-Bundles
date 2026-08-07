<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Content\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;

class ParentTag extends Criterion
{
    public function __construct(
        public string $whichParameter,
        public CriterionInterface $criterion,
        public ?string $tag = null
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
