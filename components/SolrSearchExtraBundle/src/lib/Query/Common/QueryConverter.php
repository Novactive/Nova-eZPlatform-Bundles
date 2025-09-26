<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Common;

use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\CustomField;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Solr\DocumentMapper;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Ibexa\Solr\CoreFilter\NativeCoreFilter;
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

        if (false === strpos($params['fq'], 'document_type_id:')) {
            // If there is no filter on the document type, we add it based on the query type
            $criteria = [];
            if ($query instanceof LocationQuery) {
                $criteria[] = new CustomField(
                    NativeCoreFilter::FIELD_DOCUMENT_TYPE,
                    Operator::EQ,
                    DocumentMapper::DOCUMENT_TYPE_IDENTIFIER_LOCATION
                );
            } else {
                $criteria[] = new CustomField(
                    NativeCoreFilter::FIELD_DOCUMENT_TYPE,
                    Operator::EQ,
                    DocumentMapper::DOCUMENT_TYPE_IDENTIFIER_CONTENT
                );
            }
            if (null !== $query->filter) {
                $criteria[] = $query->filter;
            }
            $query->filter = new Query\Criterion\LogicalAnd($criteria);
        }
        if ($query->filter instanceof Query\Criterion\LogicalAnd) {
            $params['fq'] = [];
            $this->flattenFilterQueries($query->filter->criteria, $params['fq']);
        }

        if ($query instanceof AdvancedContentQuery && $query->groupConfig) {
            $params = array_merge($params, $query->groupConfig);
        }
        $params['fl'] .= ',[child limit=-1 parentFilter=*:*]';

        return $params;
    }

    /**
     * @param Query\Criterion[] $criterions
     */
    public function flattenFilterQueries(array $criterions, array &$filterQuery): void
    {
        foreach ($criterions as $criterion) {
            if ($criterion instanceof Query\Criterion\LogicalAnd) {
                $this->flattenFilterQueries($criterion->criteria, $filterQuery);
            } else {
                $filterQuery[] = $this->criterionVisitor->visit($criterion);
            }
        }
    }
}
