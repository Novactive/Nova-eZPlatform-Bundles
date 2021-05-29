<?php

/**
 * NovaeZExtraBundle Content Helper.
 *
 * @package   Novactive\Bundle\eZExtraBundle
 *
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZExtraBundle\Core\Helper\eZ;

use eZ\Publish\API\Repository\Repository as RepositoryInterface;
use eZ\Publish\API\Repository\Values\Content\Content as RepositoryContent;
use eZ\Publish\API\Repository\Values\Content\LocationQuery as Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use eZ\Publish\Core\FieldType\Date\Value as DateValue;
use eZ\Publish\Core\FieldType\DateAndTime\Value as DateTimeValue;

class Content
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var WrapperFactory
     */
    protected $wrapperFactory;

    public function __construct(RepositoryInterface $repository, WrapperFactory $wrapperFactory)
    {
        $this->repository = $repository;
        $this->wrapperFactory = $wrapperFactory;
    }

    /**
     * Wrap into content/location.
     */
    protected function wrapResults(SearchResult $results, ?int $limit = null): Result
    {
        $contentResults = new Result();
        $contentResults->setResultTotalCount($results->totalCount);
        $contentResults->setResultLimit($limit);
        foreach ($results->searchHits as $hit) {
            $contentResults->addResult($this->wrapperFactory->createByLocation($hit->valueObject));
        }

        return $contentResults;
    }

    /**
     * Fetch Content (Location).
     */
    public function fetchContentQuery(
        int $parentLocationId,
        array $typeIdentifiers = [],
        array $sortClauses = [],
        array $additionnalCriterions = [],
        ?int $limit = null,
        int $offset = 0,
        string $type = 'list'
    ): Query {
        $query = new Query();

        $criterion = [];
        if ('list' === $type) {
            $criterion[] = new Criterion\ParentLocationId($parentLocationId);
        }
        if ('tree' === $type) {
            $location = $this->repository->getLocationService()->loadLocation($parentLocationId);
            $criterion[] = new Criterion\Subtree($location->pathString);
        }

        $criterion[] = new Criterion\Visibility(Criterion\Visibility::VISIBLE);

        if (count($typeIdentifiers) > 0) {
            $criterion[] = new Criterion\ContentTypeIdentifier($typeIdentifiers);
        }

        if (!empty($additionnalCriterions)) {
            $criterion = array_merge($criterion, $additionnalCriterions);
        }

        if (property_exists($query, 'query')) {
            $query->query = new Criterion\LogicalAnd($criterion);
        } else {
            $query->criterion = new Criterion\LogicalAnd($criterion);
        }

        if (!empty($sortClauses)) {
            $query->sortClauses = $sortClauses;
        }

        $query->limit = $limit ?? PHP_INT_MAX;
        $query->offset = $offset;

        return $query;
    }

    /**
     * Fetch Content (location) List.
     */
    public function contentList(
        int $parentLocationId,
        array $typeIdentifiers = [],
        array $sortClauses = [],
        ?int $limit = null,
        int $offset = 0,
        array $additionnalCriterions = []
    ): Result {
        $searchService = $this->repository->getSearchService();
        $query = $this->fetchContentQuery(
            $parentLocationId,
            $typeIdentifiers,
            $sortClauses,
            $additionnalCriterions,
            $limit,
            $offset,
            'list'
        );
        $results = $searchService->findLocations($query);

        return $this->wrapResults($results, $limit);
    }

    /**
     * Fetch Content (location) Tree.
     */
    public function contentTree(
        int $parentLocationId,
        array $typeIdentifiers = [],
        array $sortClauses = [],
        ?int $limit = null,
        int $offset = 0,
        array $additionnalCriterions = []
    ): Result {
        $searchService = $this->repository->getSearchService();
        $query = $this->fetchContentQuery(
            $parentLocationId,
            $typeIdentifiers,
            $sortClauses,
            $additionnalCriterions,
            $limit,
            $offset,
            'tree'
        );
        $results = $searchService->findLocations($query);

        return $this->wrapResults($results, $limit);
    }

    /**
     * Next By Attribute ( ASC SORT ).
     */
    public function nextByAttribute(
        int $locationId,
        string $attributeIdentifier,
        array $additionnalCriterions = []
    ): Result {
        return $this->getBy(
            $locationId,
            Criterion\Operator::GTE,
            Query::SORT_ASC,
            $attributeIdentifier,
            $additionnalCriterions
        );
    }

    /**
     * Next By Priority ( ASC SORT ).
     */
    public function nextByPriority(int $locationId, array $additionnalCriterions = []): Result
    {
        return $this->getBy($locationId, Criterion\Operator::GTE, Query::SORT_ASC, null, $additionnalCriterions);
    }

    /**
     * Previous By Attribute ( DESC SORT ).
     */
    public function previousByAttribute(
        int $locationId,
        string $attributeIdentifier,
        array $additionnalCriterions = []
    ): Result {
        return $this->getBy(
            $locationId,
            Criterion\Operator::LTE,
            Query::SORT_DESC,
            $attributeIdentifier,
            $additionnalCriterions
        );
    }

    /**
     * Previous By Priority ( DESC SORT ).
     */
    public function previousByPriority($locationId, $additionnalCriterions = []): Result
    {
        return $this->getBy($locationId, Criterion\Operator::LTE, Query::SORT_DESC, null, $additionnalCriterions);
    }

    /**
     * Get By.
     */
    protected function getBy(
        int $locationId,
        string $operator,
        string $order,
        ?string $attributeIdentifier = null,
        array $additionnalCriterions = []
    ): Result {
        $contentService = $this->repository->getContentService();
        $locationService = $this->repository->getLocationService();
        $searchService = $this->repository->getSearchService();
        $contentTypeService = $this->repository->getContentTypeService();
        $location = $locationService->loadLocation($locationId);
        $content = $contentService->loadContent($location->contentInfo->id);

        $locationContentType = $contentTypeService->loadContentType($location->contentInfo->contentTypeId);
        $sameTypeCriterion = new Criterion\ContentTypeIdentifier($locationContentType->identifier);
        $sameParentLocationCriterion = new Criterion\ParentLocationId([$location->parentLocationId]);
        $notSameLocation = new Criterion\LogicalNot(
            new Criterion\LocationId($location->id)
        );

        $criterion = $sortClause = [];

        $criterion[] = $sameTypeCriterion;
        $criterion[] = $sameParentLocationCriterion;
        $criterion[] = $notSameLocation;

        $query = new Query();

        if (null !== $attributeIdentifier) {
            // by Attribute
            $valueSortFieldValue = $content->getFieldValue($attributeIdentifier);
            if ($valueSortFieldValue instanceof DateValue) {
                $valueSortFieldValue = $valueSortFieldValue->date->getTimestamp();
            } else {
                if ($valueSortFieldValue instanceof DateTimeValue) {
                    $valueSortFieldValue = $valueSortFieldValue->value->getTimestamp();
                }
            }

            $criterion[] = new Criterion\Field($attributeIdentifier, $operator, $valueSortFieldValue);
            $sortClause[] = new SortClause\Field(
                $locationContentType->identifier,
                $attributeIdentifier,
                $order
            );
        } else {
            $criterion[] = new Criterion\Location\Priority($operator, $location->priority);
            $sortClause[] = new SortClause\Location\Priority($order);
        }

        $criterion = array_merge($criterion, $additionnalCriterions);
        if (property_exists($query, 'query')) {
            $query->query = new Criterion\LogicalAnd($criterion);
        } else {
            $query->criterion = new Criterion\LogicalAnd($criterion);
        }

        $query->sortClauses = $sortClause;
        $query->limit = 1;
        $result = $searchService->findLocations($query);

        return $this->wrapResults($result, 1);
    }

    public function getSelectionTextValue(RepositoryContent $content, string $identifier): string
    {
        $options = $content->getContentType()->getFieldDefinition($identifier);
        if (null !== $options) {
            $list = $options->getFieldSettings();
            $index = $content->getFieldValue($identifier)->selection;
            if (isset($index[0], $list['options'][$index[0]])) {
                return $list['options'][$index[0]];
            }
        }

        return '';
    }
}
