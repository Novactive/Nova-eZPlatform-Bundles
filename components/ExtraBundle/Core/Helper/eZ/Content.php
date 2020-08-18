<?php
/**
 * NovaeZExtraBundle Content Helper
 *
 * @package   Novactive\Bundle\eZExtraBundle
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */
namespace Novactive\Bundle\eZExtraBundle\Core\Helper\eZ;

use eZ\Publish\API\Repository\Repository as RepositoryInterface;
use eZ\Publish\API\Repository\Values\Content\LocationQuery as Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\Core\FieldType\Date\Value as DateValue;
use eZ\Publish\Core\FieldType\DateAndTime\Value as DateTimeValue;

/**
 * Class Content
 */
class Content
{

    /**
     * Repository eZ Publish
     *
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * WrapperFactory
     *
     * @var WrapperFactory
     */
    protected $wrapperFactory;

    /**
     * Constructor
     *
     * @param RepositoryInterface $repository
     * @param WrapperFactory      $wrapperFactory
     */
    public function __construct(RepositoryInterface $repository, WrapperFactory $wrapperFactory)
    {
        $this->repository     = $repository;
        $this->wrapperFactory = $wrapperFactory;
    }

    /**
     * Wrap into content/location
     *
     * @param mixed   $results
     * @param integer $limit
     *
     * @return Result
     */
    protected function wrapResults($results, $limit)
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
     * Fetch Content (Location)
     *
     * @param integer $parentLocationId
     * @param array   $typeIdentifiers
     * @param array   $sortClauses
     * @param array   $additionnalCriterions
     * @param null    $limit
     * @param int     $offset
     * @param string  $type
     *
     * @return Query
     */
    public function fetchContentQuery(
        $parentLocationId,
        $typeIdentifiers = [],
        $sortClauses = [],
        $additionnalCriterions = [],
        $limit = null,
        $offset = 0,
        $type = 'list'
    ) {
        $query = new Query();

        $criterion = [];
        if ($type == 'list') {
            $criterion[] = new Criterion\ParentLocationId($parentLocationId);
        }
        if ($type == 'tree') {
            $location    = $this->repository->getLocationService()->loadLocation($parentLocationId);
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

        $query->limit  = $limit === null ? PHP_INT_MAX : $limit;
        $query->offset = $offset;

        return $query;
    }

    /**
     * Fetch Content (location) List
     *
     * @param integer $parentLocationId
     * @param array   $typeIdentifiers
     * @param array   $sortClauses
     * @param null    $limit
     * @param int     $offset
     * @param array   $additionnalCriterions
     *
     * @return Result
     */
    public function contentList(
        $parentLocationId,
        $typeIdentifiers = [],
        $sortClauses = [],
        $limit = null,
        $offset = 0,
        $additionnalCriterions = []
    ) {
        $searchService = $this->repository->getSearchService();
        $query         = $this->fetchContentQuery(
            $parentLocationId,
            $typeIdentifiers,
            $sortClauses,
            $additionnalCriterions,
            $limit,
            $offset,
            'list'
        );
        $results       = $searchService->findLocations($query);

        return $this->wrapResults($results, $limit);
    }

    /**
     * Fetch Content (location) Tree
     *
     * @param integer $parentLocationId
     * @param array   $typeIdentifiers
     * @param array   $sortClauses
     * @param null    $limit
     * @param int     $offset
     * @param array   $additionnalCriterions
     *
     * @return Result
     */
    public function contentTree(
        $parentLocationId,
        $typeIdentifiers = [],
        $sortClauses = [],
        $limit = null,
        $offset = 0,
        $additionnalCriterions = []
    ) {
        $searchService = $this->repository->getSearchService();
        $query         = $this->fetchContentQuery(
            $parentLocationId,
            $typeIdentifiers,
            $sortClauses,
            $additionnalCriterions,
            $limit,
            $offset,
            'tree'
        );
        $results       = $searchService->findLocations($query);

        return $this->wrapResults($results, $limit);
    }

    /**
     * Next By Attribute ( ASC SORT )
     *
     * @param integer $locationId
     * @param string  $attributeIdentifier
     * @param string  $locale
     * @param array   $additionnalCriterions
     *
     * @return Result
     */
    public function nextByAttribute($locationId, $attributeIdentifier, $locale = "eng-US", $additionnalCriterions = [])
    {
        return $this->getBy(
            $locationId,
            Criterion\Operator::GTE,
            Query::SORT_ASC,
            $attributeIdentifier,
            $additionnalCriterions,
            $locale
        );
    }

    /**
     * Next By Priority ( ASC SORT )
     *
     * @param integer $locationId
     * @param array   $additionnalCriterions
     *
     * @return Result
     */
    public function nextByPriority($locationId, $additionnalCriterions = [])
    {
        return $this->getBy($locationId, Criterion\Operator::GTE, Query::SORT_ASC, null, $additionnalCriterions);
    }

    /**
     * Previous By Attribute ( DESC SORT )
     *
     * @param integer $locationId
     * @param string  $attributeIdentifier
     * @param array   $additionnalCriterions
     * @param string  $locale
     *
     * @return Result
     */
    public function previousByAttribute(
        $locationId,
        $attributeIdentifier,
        $locale = "eng-US",
        $additionnalCriterions = []
    ) {
        return $this->getBy(
            $locationId,
            Criterion\Operator::LTE,
            Query::SORT_DESC,
            $attributeIdentifier,
            $additionnalCriterions,
            $locale
        );
    }

    /**
     * Previous By Priority ( DESC SORT )
     *
     * @param integer $locationId
     * @param array   $additionnalCriterions
     *
     * @return Result
     */
    public function previousByPriority($locationId, $additionnalCriterions = [])
    {
        return $this->getBy($locationId, Criterion\Operator::LTE, Query::SORT_DESC, null, $additionnalCriterions);
    }

    /**
     * Get By
     *
     * @param integer     $locationId
     * @param integer     $operator
     * @param integer     $order
     * @param string|null $attributeIdentifier
     * @param array       $additionnalCriterions
     * @param string      $locale
     *
     * @return Result
     */
    protected function getBy(
        $locationId,
        $operator,
        $order,
        $attributeIdentifier = null,
        $additionnalCriterions = [],
        $locale = "eng-US"
    ) {
        $contentService     = $this->repository->getContentService();
        $locationService    = $this->repository->getLocationService();
        $searchService      = $this->repository->getSearchService();
        $contentTypeService = $this->repository->getContentTypeService();
        $location           = $locationService->loadLocation($locationId);
        $content            = $contentService->loadContent($location->contentInfo->id);

        $locationContentType         = $contentTypeService->loadContentType($location->contentInfo->contentTypeId);
        $sameTypeCriterion           = new Criterion\ContentTypeIdentifier($locationContentType->identifier);
        $sameParentLocationCriterion = new Criterion\ParentLocationId(array ($location->parentLocationId));
        $notSameLocation             = new Criterion\LogicalNot(
            new Criterion\LocationId($location->id)
        );

        $criterion[] = $sameTypeCriterion;
        $criterion[] = $sameParentLocationCriterion;
        $criterion[] = $notSameLocation;

        $query = new Query();

        if ($attributeIdentifier != null) {
            // by Attribute
            $valueSortFieldValue = $content->getFieldValue($attributeIdentifier);
            if ($valueSortFieldValue instanceof DateValue) {
                $valueSortFieldValue = $valueSortFieldValue->date->getTimestamp();
            } else {
                if ($valueSortFieldValue instanceof DateTimeValue) {
                    $valueSortFieldValue = $valueSortFieldValue->value->getTimestamp();
                }
            }

            $criterion[]  = new Criterion\Field($attributeIdentifier, $operator, $valueSortFieldValue);
            $sortClause[] = new SortClause\Field(
                $locationContentType->identifier, $attributeIdentifier, $order, $locale
            );
        } else {
            $criterion[]  = new Criterion\Location\Priority($operator, $location->priority);
            $sortClause[] = new SortClause\Location\Priority($order);
        }

        $criterion = array_merge($criterion, $additionnalCriterions);
        if (property_exists($query, 'query')) {
            $query->query = new Criterion\LogicalAnd($criterion);
        } else {
            $query->criterion = new Criterion\LogicalAnd($criterion);
        }

        $query->sortClauses = $sortClause;
        $query->limit       = 1;
        $result             = $searchService->findLocations($query);

        return $this->wrapResults($result, 1);
    }
}
