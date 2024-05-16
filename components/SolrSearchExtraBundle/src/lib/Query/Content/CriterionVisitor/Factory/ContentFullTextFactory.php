<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Content\CriterionVisitor\Factory;

use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Ibexa\Core\Search\Common\FieldNameResolver;
use Ibexa\Solr\FieldMapper\IndexingDepthProvider;
use Novactive\EzSolrSearchExtra\Query\Content\CriterionVisitor\MultipleFieldsFullText;
use QueryTranslator\Languages\Galach\Generators\ExtendedDisMax;
use QueryTranslator\Languages\Galach\Parser;
use QueryTranslator\Languages\Galach\Tokenizer;

class ContentFullTextFactory
{
    /**
     * Create from content type handler and field registry.
     */
    public function __construct(
        protected FieldNameResolver $fieldNameResolver,
        protected Tokenizer $tokenizer,
        protected Parser $parser,
        protected ExtendedDisMax $generator,
        protected IndexingDepthProvider $indexingDepthProvider
    ) {
    }

    /**
     * Create FullText Criterion Visitor.
     *
     * @return \Ibexa\Contracts\Solr\Query\CriterionVisitor|\Ibexa\Solr\Query\Content\CriterionVisitor\FullText
     */
    public function createCriterionVisitor(): CriterionVisitor
    {
        return new MultipleFieldsFullText(
            $this->fieldNameResolver,
            $this->tokenizer,
            $this->parser,
            $this->generator,
            $this->indexingDepthProvider->getMaxDepth()
        );
    }
}
