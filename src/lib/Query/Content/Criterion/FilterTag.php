<?php

/**
 * @copyright Novactive
 * Date: 10/01/2020
 */

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Content\Criterion;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

class FilterTag extends Criterion
{
    /** @var string */
    public $tag;

    /** @var Criterion */
    public $criterion;

    /**
     * FilterTag constructor.
     */
    public function __construct(string $tag, Criterion $criterion)
    {
        $this->tag       = $tag;
        $this->criterion = $criterion;
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecifications()
    {
        return [];
    }
}
