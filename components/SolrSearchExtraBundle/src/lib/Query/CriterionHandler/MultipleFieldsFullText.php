<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\CriterionHandler;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\FullText;
use Novactive\EzSolrSearchExtra\Query\Content\Criterion\MultipleFieldsFullText as MultipleFieldsFullTextCriterion;

class MultipleFieldsFullText extends FullText
{
    /**
     * Check if this criterion handler accepts to handle the given criterion.
     */
    public function accept(CriterionInterface $criterion): bool
    {
        return $criterion instanceof MultipleFieldsFullTextCriterion;
    }
}
