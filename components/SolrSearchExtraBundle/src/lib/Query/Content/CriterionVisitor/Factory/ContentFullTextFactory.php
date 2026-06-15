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
     * Field map.
     *
     * @var FieldNameResolver
     */
    protected $fieldNameResolver;

    /**
     * @var Tokenizer
     */
    protected $tokenizer;

    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var ExtendedDisMax
     */
    protected $generator;

    /**
     * @var IndexingDepthProvider
     */
    protected $indexingDepthProvider;

    /**
     * Create from content type handler and field registry.
     */
    public function __construct(
        FieldNameResolver $fieldNameResolver,
        Tokenizer $tokenizer,
        Parser $parser,
        ExtendedDisMax $generator,
        IndexingDepthProvider $indexingDepthProvider
    ) {
        $this->fieldNameResolver = $fieldNameResolver;
        $this->tokenizer = $tokenizer;
        $this->parser = $parser;
        $this->generator = $generator;
        $this->indexingDepthProvider = $indexingDepthProvider;
    }

    /**
     * Create FullText Criterion Visitor.
     *
     * @return CriterionVisitor|\Ibexa\Solr\Query\Content\CriterionVisitor\FullText
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
