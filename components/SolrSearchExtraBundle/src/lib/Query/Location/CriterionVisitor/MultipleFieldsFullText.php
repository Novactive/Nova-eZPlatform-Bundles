<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Location\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Novactive\EzSolrSearchExtra\Query\Content\CriterionVisitor\MultipleFieldsFullText as ContentMultipleFieldsFullText;

class MultipleFieldsFullText extends ContentMultipleFieldsFullText
{
    /**
     * Map field value to a proper Solr representation.
     *
     * @return string
     */
    public function visit(Criterion $criterion, CriterionVisitor $subVisitor = null)
    {
        $condition = $this->escapeQuote(parent::visit($criterion, $subVisitor));

        return "{!child of='document_type_id:content' v='document_type_id:content AND {$condition}'}";
    }
}
