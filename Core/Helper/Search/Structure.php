<?php
/**
 * NovaeZExtraBundle Search Structure
 *
 * @package   Novactive\Bundle\eZExtraBundle
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZSEOBundle/blob/master/LICENSE MIT Licence
 */
namespace Novactive\Bundle\eZExtraBundle\Core\Helper\Search;

/**
 * Class Search Structure
 */
class Structure
{
    /**
     * Query
     *
     * @var string
     */
    protected $query;

    /**
     * Subtree Array
     *
     * @var array
     */
    protected $subtree;

    /**
     * Filters
     *
     * @var array
     */
    protected $filters;

    /**
     * Facets
     *
     * @var array
     */
    protected $facets;

    /**
     * $contentTypesIds
     *
     * @var array
     */
    protected $contentTypesIds;

    /**
     * Sort Oder
     *
     * @var array
     */
    protected $sortOrder;

    /**
     * Spell Check
     *
     * @var array
     */
    protected $spellCheck;

    /**
     * The Limit
     *
     * @var integer
     */
    protected $limit;

    /**
     * The Offset
     * @var integer
     */
    protected $offset;

    /**
     * Clustering
     *
     * @var array
     */
    protected $clustering;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->limit      = 25;
        $this->offset     = 0;
        $this->spellCheck = true;
        $this->clustering = [ 'clustering' => false ];
        $this->sortOrder  = [ 'score' => 'desc' ];
        $this->facets     = [];
        $this->filters    = [];
    }

    /**
     * Get eZ Find Legacy Query
     *
     * @return array
     */
    public function geteZLegacyFindQuery()
    {
        $ezfQuery            = [];
        $ezfQuery['query']   = $this->getQuery();
        $ezfQuery['limit']   = $this->getLimit();
        $ezfQuery['offset']  = $this->getOffset();
        $ezfQuery['sort_by'] = $this->getSortOrder();
        $ezfQuery['search_result_clustering'] = $this->getClustering();

        if ( $this->getFilters() !== null )
        {
            $ezfQuery['filter'] = $this->getFilters();
        }

        if ( $this->getSubtree() !== null )
        {
            $ezfQuery['subtree_array'] = $this->getSubtree();
        }

        if ( $this->getContentTypesIds() !== null )
        {
            $ezfQuery['class_id'] = $this->getContentTypesIds();
        }

        if ( $this->getFacets() !== null )
        {
            $ezfQuery['facet'] = $this->getFacets();
        }

        if ( $this->getSpellCheck() !== null )
        {
            $ezfQuery['spell_check'] = [ $this->getSpellCheck() ];
        }

        return $ezfQuery;
    }

    /**
     * Get the Query
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set the Query
     *
     * @param string $query
     *
     * @return $this
     */
    public function setQuery( $query )
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get the Subtree
     *
     * @return array
     */
    public function getSubtree()
    {
        return $this->subtree;
    }

    /**
     * Set the Subtree
     *
     * @param array $subtree
     *
     * @return $this
     */
    public function setSubtree( $subtree )
    {
        $this->subtree = $subtree;

        return $this;
    }

    /**
     * Get the Filters
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Set the Filters
     *
     * @param array $filters
     *
     * @return $this
     */
    public function setFilters( $filters )
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * Add filter
     *
     * @param array $filters
     *
     * @return $this
     */
    public function addFilters( $filters )
    {
        foreach ( $filters as $filter )
        {
            if ( !$this->hasFilter( $filter ) )
            {
                $this->filters[] = $filter;
            }
        }

        return $this;
    }

    /**
     * Get the Facets
     *
     * @return array
     */
    public function getFacets()
    {
        return $this->facets;
    }

    /**
     * Set the Facets
     *
     * @param array $facets
     *
     * @return $this
     */
    public function setFacets( $facets )
    {
        $this->facets = $facets;

        return $this;
    }

    /**
     * Get the ContentTypesIds
     *
     * @return array
     */
    public function getContentTypesIds()
    {
        return $this->contentTypesIds;
    }

    /**
     * Set the ContentTypesIds
     *
     * @param array $contentTypesIds
     *
     * @return $this
     */
    public function setContentTypesIds( $contentTypesIds )
    {
        $this->contentTypesIds = $contentTypesIds;

        return $this;
    }

    /**
     * Get the SortOrder
     *
     * @return array
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * Set the SortOrder
     *
     * @param array $sortOrder
     *
     * @return $this
     */
    public function setSortOrder( $sortOrder )
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * Get the SpellCheck
     *
     * @return array
     */
    public function getSpellCheck()
    {
        return $this->spellCheck;
    }

    /**
     * Set the SpellCheck
     *
     * @param array $spellCheck
     *
     * @return $this
     */
    public function setSpellCheck( $spellCheck )
    {
        $this->spellCheck = $spellCheck;

        return $this;
    }

    /**
     * Get the Limit
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set the Limit
     *
     * @param integer $limit
     *
     * @return $this
     */
    public function setLimit( $limit )
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Get the Offset
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Set the Offset
     *
     * @param integer $offset
     *
     * @return $this
     */
    public function setOffset( $offset )
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Set the Page ( offset *= limit )
     *
     * @param integer $page
     *
     * @return $this
     */
    public function setPage( $page )
    {
        if ( $page < 1 )
        {
            $this->offset = 0;
        }
        $this->offset = ( $page - 1 ) * $this->getLimit();

        return $this;
    }

    /**
     * Get the Clustering
     *
     * @return array
     */
    public function getClustering()
    {
        return $this->clustering;
    }

    /**
     * Set the Clustering
     *
     * @param array $clustering
     *
     * @return $this
     */
    public function setClustering( $clustering )
    {
        $this->clustering = $clustering;

        return $this;
    }

    /**
     * Is there something to show
     *
     * @param array $nameList
     *
     * @return bool
     */
    public function hasToShowFacetName( $nameList )
    {
        return ( count( $nameList ) >= 2 );
    }

    /**
     * Has Filter
     *
     * @param array $filter
     *
     * @return bool
     */
    public function hasFilter( $filter )
    {
        return in_array( $filter, $this->filters );
    }

    /**
     * Map SolR Attributes to FacetName
     *
     * @return array
     */
    public function getNamedFilters()
    {
        $filtersNamed = [];
        foreach ( $this->filters as $filter )
        {
            list( $solrAttribute, $solrValue ) = explode( ":", $filter );
            $solrValue = trim( $solrValue, '"' );
            foreach ( $this->facets as $facetInfos )
            {
                if ( preg_match( "/{$solrAttribute}/", $facetInfos['field'] ) )
                {
                    $filtersNamed[$filter]['name'] = $facetInfos['name'];
                    if ( isset( $facetInfos['map'] ) && isset( $facetInfos['map'][$solrValue] ) )
                    {
                        $solrValue = $facetInfos['map'][$solrValue];
                    }
                    $filtersNamed[$filter]['value'] = $solrValue;
                }
            }
        }

        return $filtersNamed;
    }
}
