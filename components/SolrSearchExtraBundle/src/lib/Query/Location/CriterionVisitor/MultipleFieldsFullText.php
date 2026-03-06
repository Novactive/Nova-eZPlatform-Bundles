<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Location\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Novactive\EzSolrSearchExtra\Query\Content\CriterionVisitor\MultipleFieldsFullText as ContentMultipleFieldsFullText;

class MultipleFieldsFullText extends ContentMultipleFieldsFullText
{
    public function visit(CriterionInterface $criterion, ?CriterionVisitor $subVisitor = null): string
    {
        $condition = $this->escapeQuote(parent::visit($criterion, $subVisitor));

        return "{!child of='document_type_id:content' v='document_type_id:content AND {$condition}'}";
    }
}
