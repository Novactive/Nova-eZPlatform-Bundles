<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Content\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

class FilterTag extends Criterion
{
    /**
     * @var string
     */
    public $tag;

    /**
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion
     */
    public $criterion;

    /**
     * FilterTag constructor.
     */
    public function __construct(string $tag, Criterion $criterion)
    {
        $this->tag = $tag;
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
