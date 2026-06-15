<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Content\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Ibexa\Core\Search\Common\FieldNameResolver;
use Novactive\EzSolrSearchExtra\Query\Content\Criterion\MultipleFieldsFullText as MultipleFieldsFullTextCriterion;
use QueryTranslator\Languages\Galach\Generators\ExtendedDisMax;
use QueryTranslator\Languages\Galach\Parser;
use QueryTranslator\Languages\Galach\Tokenizer;

class MultipleFieldsFullText extends CriterionVisitor
{
    public function __construct(
        protected FieldNameResolver $fieldNameResolver,
        protected Tokenizer $tokenizer,
        protected Parser $parser,
        protected ExtendedDisMax $generator,
        protected int $maxDepth = 0
    ) {
    }

    /**
     * @return array<string, \Ibexa\Contracts\Core\Search\FieldType>
     */
    protected function getSearchFields(Criterion $criterion, string $fieldDefinitionIdentifier): array
    {
        return $this->fieldNameResolver->getFieldTypes($criterion, $fieldDefinitionIdentifier);
    }

    public function canVisit(CriterionInterface $criterion): bool
    {
        return $criterion instanceof MultipleFieldsFullTextCriterion;
    }

    /**
     * @param MultipleFieldsFullTextCriterion $criterion
     */
    public function visit(CriterionInterface $criterion, ?CriterionVisitor $subVisitor = null): string
    {
        /** @var MultipleFieldsFullTextCriterion $criterion */
        $tokenSequence = $this->tokenizer->tokenize($criterion->value);
        $syntaxTree = $this->parser->parse($tokenSequence);

        $options = [];
        if ($criterion->fuzziness < 1) {
            $options['fuzziness'] = $criterion->fuzziness;
        }

        $queryString = $this->generator->generate($syntaxTree, $options);

        if (true === $criterion->wildcards) {
            $wildcardQueryString = $this->generator->generate($syntaxTree, ['wildcard' => true]);
            $queryString .= sprintf(' OR %s', $wildcardQueryString);
        }

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
        if (!empty($criterion->boostQueries)) {
            $queryParams['bq'] = $criterion->boostQueries;
        }

        $queryParamsString = implode(' ', array_map(function ($key, $value) {
            if (is_array($value)) {
                return implode(' ', array_map(fn ($value) => "{$key}='{$value}'", $value));
            }

            return "{$key}='{$value}'";
        }, array_keys($queryParams), $queryParams));

        return "{!edismax {$queryParamsString}}";
    }

    private function getQueryFields(CriterionInterface $criterion): string
    {
        /** @var MultipleFieldsFullTextCriterion $criterion */
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

    private function getBoostFactorForRelatedContent(int $depth): float
    {
        return 1.0 / 2.0 ** $depth;
    }
}
