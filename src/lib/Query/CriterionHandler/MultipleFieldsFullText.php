<?php

/**
 * NovaeZSolrSearchExtraBundle.
 *
 * @package   NovaeZSolrSearchExtraBundle
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZSolrSearchExtraBundle/blob/master/LICENSE
 */

namespace Novactive\EzSolrSearchExtra\Query\CriterionHandler;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\FullText;
use Novactive\EzSolrSearchExtra\Query\Content\Criterion\MultipleFieldsFullText as MultipleFieldsFullTextCriterion;

class MultipleFieldsFullText extends FullText
{
    /**
     * Check if this criterion handler accepts to handle the given criterion.
     *
     * @return bool
     */
    public function accept(Criterion $criterion)
    {
        return $criterion instanceof MultipleFieldsFullTextCriterion;
    }
}
