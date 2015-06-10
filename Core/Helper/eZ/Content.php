<?php
/**
 * NovaeZExtraBundle Content Helper
 *
 * @package   Novactive\Bundle\eZExtraBundle
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZSEOBundle/blob/master/LICENSE MIT Licence
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
     * Constructor
     *
     * @param RepositoryInterface $repository
     */
    public function __construct( RepositoryInterface $repository )
    {
        $this->repository = $repository;
    }

    /**
     * Wrap into content/location
     *
     * @param mixed   $results
     * @param integer $limit
     *
     * @return Result
     */
    protected function wrapResults( $results, $limit )
    {
        $contentResults = new Result();
        $contentResults->setResultTotalCount( $results->totalCount );
        $contentResults->setResultLimit( $limit );
        foreach ( $results->searchHits as $hit )
        {
            $location = $hit->valueObject;
            $content  = $this->repository->getContentService()->loadContentByContentInfo( $location->contentInfo );
            $contentResults->addResult( [ 'content' => $content, 'location' => $location ] );
        }
        return $contentResults;
    }

    /**
     * Fetch Content (Location)
     *
     * @param integer $parentLocationId
     * @param array   $typeIdentifiers
     * @param array   $sortClauses
     * @param null    $limit
     * @param int     $offset
     * @param string  $type
     *
     * @return Query
     */
    protected function fetchContentQuery(
        $parentLocationId,
        $typeIdentifiers = [],
        $sortClauses = [],
        $limit = null,
        $offset = 0,
        $type = 'list'
    )
    {
        $query       = new Query();

        $criterion = [];
        if ( $type == 'list' )
        {
            $criterion[] = new Criterion\ParentLocationId( $parentLocationId );
        }
        if ( $type == 'tree' )
        {
            $location = $this->repository->getLocationService()->loadLocation( $parentLocationId );
            $criterion[] = new Criterion\Subtree( $location->pathString );
        }

        $criterion[] = new Criterion\Visibility( Criterion\Visibility::VISIBLE );

        if ( count( $typeIdentifiers ) > 0 )
        {
            $criterion[] = new Criterion\ContentTypeIdentifier( $typeIdentifiers );
        }

        $query->criterion = new Criterion\LogicalAnd( $criterion );
        if ( !empty( $sortClauses ) )
        {
            $query->sortClauses = $sortClauses;
        }
        $query->limit  = $limit;
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
     *
     * @return Result
     */
    public function contentList(
        $parentLocationId,
        $typeIdentifiers = [],
        $sortClauses = [],
        $limit = null,
        $offset = 0
    )
    {
        $searchService = $this->repository->getSearchService();
        $query = $this->fetchContentQuery( $parentLocationId, $typeIdentifiers, $sortClauses, $limit, $offset, 'list' );
        $results       = $searchService->findLocations( $query );
        return $this->wrapResults( $results, $limit );
    }

    /**
     * Fetch Content (location) Tree
     *
     * @param integer $parentLocationId
     * @param array   $typeIdentifiers
     * @param array   $sortClauses
     * @param null    $limit
     * @param int     $offset
     *
     * @return Result
     */
    public function contentTree(
        $parentLocationId,
        $typeIdentifiers = [],
        $sortClauses = [],
        $limit = null,
        $offset = 0
    )
    {
        $searchService = $this->repository->getSearchService();
        $query = $this->fetchContentQuery( $parentLocationId, $typeIdentifiers, $sortClauses, $limit, $offset, 'tree' );
        $results       = $searchService->findLocations( $query );
        return $this->wrapResults( $results, $limit );
    }

    /**
     * Next By Attribute ( ASC SORT )
     *
     * @param integer $locationId
     * @param string  $attributeIdentifier
     *
     * @return Result
     */
    public function nextByAttribute( $locationId, $attributeIdentifier )
    {
        return $this->getBy( $locationId, Criterion\Operator::GTE, Query::SORT_ASC, $attributeIdentifier );
    }

    /**
     * Next By Priority ( ASC SORT )
     *
     * @param integer $locationId
     *
     * @return Result
     */
    public function nextByPriority( $locationId )
    {
        return $this->getBy( $locationId, Criterion\Operator::GTE, Query::SORT_ASC );
    }

    /**
     * Previous By Attribute ( DESC SORT )
     *
     * @param integer $locationId
     * @param string  $attributeIdentifier
     *
     * @return Result
     */
    public function previousByAttribute( $locationId, $attributeIdentifier )
    {
        return $this->getBy( $locationId, Criterion\Operator::LTE, Query::SORT_DESC, $attributeIdentifier );
    }

    /**
     * Previous By Priority ( DESC SORT )
     *
     * @param integer $locationId
     *
     * @return Result
     */
    public function previousByPriority( $locationId )
    {
        return $this->getBy( $locationId, Criterion\Operator::LTE, Query::SORT_DESC );
    }

    /**
     * Get By
     *
     * @param integer     $locationId
     * @param integer     $operator
     * @param integer     $order
     * @param string|null $attributeIdentifier
     *
     * @return Result
     */
    protected function getBy( $locationId, $operator, $order, $attributeIdentifier = null )
    {
        $contentService     = $this->repository->getContentService();
        $locationService    = $this->repository->getLocationService();
        $searchService      = $this->repository->getSearchService();
        $contentTypeService = $this->repository->getContentTypeService();
        $location           = $locationService->loadLocation( $locationId );
        $content            = $contentService->loadContent( $location->contentInfo->id );

        $locationContentType         = $contentTypeService->loadContentType( $location->contentInfo->contentTypeId );
        $sameTypeCriterion           = new Criterion\ContentTypeIdentifier( $locationContentType->identifier );
        $sameParentLocationCriterion = new Criterion\ParentLocationId( array( $location->parentLocationId ) );
        $notSameLocation             = new Criterion\LogicalNot(
            new Criterion\LocationId( $location->id )
        );
        $query                       = new Query();

        if ( $attributeIdentifier != null )
        {
            // by Attribute
            $valueSortFieldValue = $content->getFieldValue( $attributeIdentifier );
            if ( $valueSortFieldValue instanceof DateValue )
            {
                $valueSortFieldValue = $valueSortFieldValue->date->getTimestamp();
            }
            else
            {
                if ( $valueSortFieldValue instanceof DateTimeValue )
                {
                    $valueSortFieldValue = $valueSortFieldValue->value->getTimestamp();
                }
            }
            $query->criterion   = new Criterion\LogicalAnd(
                [
                    $sameTypeCriterion,
                    $sameParentLocationCriterion,
                    $notSameLocation,
                    new Criterion\Field( $attributeIdentifier, $operator, $valueSortFieldValue ),
                ]
            );
            $query->sortClauses = array(
                new SortClause\Field( $locationContentType->identifier, $attributeIdentifier, $order )
            );
        }
        else
        {
            // by Priority
            $query->criterion   = new Criterion\LogicalAnd(
                [
                    $sameTypeCriterion,
                    $sameParentLocationCriterion,
                    $notSameLocation,
                    new Criterion\Location\Priority( $operator, $location->priority ),
                ]
            );
            $query->sortClauses = array( new SortClause\Location\Priority( $order ) );
        }
        $query->limit = 1;
        $result       = $searchService->findLocations( $query );

        return $this->wrapResults( $result, 1 );
    }
}
