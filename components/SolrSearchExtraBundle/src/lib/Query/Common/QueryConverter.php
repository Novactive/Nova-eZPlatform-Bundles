<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Common;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Ibexa\Solr\Query\Common\QueryConverter\NativeQueryConverter;
use Ibexa\Solr\Query\QueryConverter as BaseQueryConverter;
use Novactive\EzSolrSearchExtra\Query\AdvancedContentQuery;

class QueryConverter extends NativeQueryConverter
{
    /** @var BaseQueryConverter */
    protected $baseConverter;

    /** @var \Ibexa\Contracts\Solr\Query\CriterionVisitor */
    protected $criterionVisitor;

    /**
     * QueryConverter constructor.
     */
    public function __construct(BaseQueryConverter $baseConverter, CriterionVisitor $criterionVisitor)
    {
        $this->baseConverter = $baseConverter;
        $this->criterionVisitor = $criterionVisitor;
    }

    /**
     * {@inheritdoc}
     */
    public function convert(Query $query, array $languageSettings = []): array
    {
        $params = $this->baseConverter->convert($query, $languageSettings);

        if ($query->filter instanceof Query\Criterion\LogicalAnd) {
            $params['fq'] = [];
            foreach ($query->filter->criteria as $criterion) {
                if ($criterion instanceof Query\Criterion\LogicalAnd) {
                    foreach ($criterion->criteria as $subcriterion) {
                        $params['fq'][] = $this->criterionVisitor->visit($subcriterion);
                    }
                } else {
                    $params['fq'][] = $this->criterionVisitor->visit($criterion);
                }
            }
        }

        if ($query instanceof AdvancedContentQuery && $query->groupConfig) {
            $params = array_merge($params, $query->groupConfig);
        }

        return $params;
    }
}
