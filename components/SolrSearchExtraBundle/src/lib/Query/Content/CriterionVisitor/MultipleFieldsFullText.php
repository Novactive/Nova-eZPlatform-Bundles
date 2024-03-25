<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Content\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Ibexa\Core\Search\Common\FieldNameResolver;
use Novactive\EzSolrSearchExtra\Query\Content\Criterion\MultipleFieldsFullText as MultipleFieldsFullTextCriterion;
use QueryTranslator\Languages\Galach\Generators\ExtendedDisMax;
use QueryTranslator\Languages\Galach\Parser;
use QueryTranslator\Languages\Galach\Tokenizer;

class MultipleFieldsFullText extends CriterionVisitor
{
    /**
     * Field map.
     *
     * @var \Ibexa\Core\Search\Common\FieldNameResolver
     */
    protected $fieldNameResolver;

    /**
     * @var \QueryTranslator\Languages\Galach\Tokenizer
     */
    protected $tokenizer;

    /**
     * @var \QueryTranslator\Languages\Galach\Parser
     */
    protected $parser;

    /**
     * @var \QueryTranslator\Languages\Galach\Generators\ExtendedDisMax
     */
    protected $generator;

    /**
     * @var int
     */
    protected $maxDepth;

    /**
     * Create from content type handler and field registry.
     *
     * @param int $maxDepth
     */
    public function __construct(
        FieldNameResolver $fieldNameResolver,
        Tokenizer $tokenizer,
        Parser $parser,
        ExtendedDisMax $generator,
        $maxDepth = 0
    ) {
        $this->fieldNameResolver = $fieldNameResolver;
        $this->tokenizer = $tokenizer;
        $this->parser = $parser;
        $this->generator = $generator;
        $this->maxDepth = $maxDepth;
    }

    /**
     * Get field type information.
     *
     * @param string $fieldDefinitionIdentifier
     *
     * @return array
     */
    protected function getSearchFields(Criterion $criterion, $fieldDefinitionIdentifier)
    {
        return $this->fieldNameResolver->getFieldTypes($criterion, $fieldDefinitionIdentifier);
    }

    /**
     * Check if visitor is applicable to current criterion.
     *
     * @return bool
     */
    public function canVisit(Criterion $criterion)
    {
        return $criterion instanceof MultipleFieldsFullTextCriterion;
    }

    /**
     * Map field value to a proper Solr representation.
     *
     * @return string
     */
    public function visit(Criterion $criterion, CriterionVisitor $subVisitor = null)
    {
        /** @var \Novactive\EzSolrSearchExtra\Query\Content\Criterion\MultipleFieldsFullText $criterion */
        $tokenSequence = $this->tokenizer->tokenize($criterion->value);
        $syntaxTree = $this->parser->parse($tokenSequence);

        $options = [];
        if ($criterion->fuzziness < 1) {
            $options['fuzziness'] = $criterion->fuzziness;
        }

        $queryString = $this->generator->generate($syntaxTree, $options);
        $queryStringEscaped = $this->escapeQuote($queryString);
        $queryFields = $this->getQueryFields($criterion);

        $queryParams = [
            'v' => $queryStringEscaped,
            'qf' => $queryFields,
            'pf' => $queryFields,
            'tie' => 0.1,
            'uf' => '-*',
        ];
        $boostFunction = $criterion->boostFunctions;
        if ($criterion->boostPublishDate) {
            $boostFunction[] = 'recip(ms(NOW/HOUR,meta_publishdate__date_dt),3.16e-11,1,1)';
        }
        if (!empty($boostFunction)) {
            $queryParams['bf'] = 1 === count($boostFunction) ?
                reset($boostFunction) :
                sprintf('sum(%s)', implode(',', $boostFunction));
        }

        $queryParamsString = implode(' ', array_map(function ($key, $value) {
            return "{$key}='{$value}'";
        }, array_keys($queryParams), $queryParams));

        return "{!edismax {$queryParamsString}}";
    }

    private function getQueryFields(Criterion $criterion): string
    {
        /** @var \Novactive\EzSolrSearchExtra\Query\Content\Criterion\MultipleFieldsFullText $criterion */
        $queryFields = ['meta_content__text_t', 'meta_content__text_t_raw'];

        for ($i = 1; $i <= $this->maxDepth; ++$i) {
            $queryFields[] = "meta_related_content_{$i}__text_t^{$this->getBoostFactorForRelatedContent($i)}";
        }

        foreach ($criterion->boost as $field => $boost) {
            $searchFields = $this->getSearchFields($criterion, $field);

            foreach (array_keys($searchFields) as $name) {
                $queryFields[] = "{$name}^{$boost}";
            }
        }

        foreach ($criterion->metaBoost as $field => $boost) {
            $queryFields[] = "meta_{$field}__text_t^{$boost}";
            $queryFields[] = "meta_{$field}__text_t_raw^{$boost}";
        }

        return implode(' ', $queryFields);
    }

    /**
     * Returns boost factor for the related content.
     */
    private function getBoostFactorForRelatedContent(int $depth): float
    {
        return 1.0 / pow(2.0, $depth);
    }
}
