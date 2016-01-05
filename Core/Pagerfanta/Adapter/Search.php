<?php
/**
 * NovaeZExtraBundle Search Adapter
 *
 * @package   Novactive\Bundle\eZExtraBundle
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */
namespace Novactive\Bundle\eZExtraBundle\Core\Pagerfanta\Adapter;

use Pagerfanta\Adapter\AdapterInterface;
use Novactive\Bundle\eZExtraBundle\Core\Helper\eZ\Search as SearchHelper;
use Novactive\Bundle\eZExtraBundle\Core\Helper\Search\Structure;

/**
 * Class Search
 */
class Search implements AdapterInterface
{

    /**
     * Helper for the executing the query
     *
     * @var SearchHelper
     */
    protected $helper;

    /**
     * Structure Query
     *
     * @var Structure
     */
    protected $query;

    /**
     * Cache nbResults
     *
     * @var integer
     */
    protected $nbResults;

    /**
     * Constructor
     *
     * @param SearchHelper $helper
     * @param Structure    $query
     */
    public function __construct( SearchHelper $helper, Structure $query )
    {
        $this->helper = $helper;
        $this->query  = $query;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        if ( isset( $this->nbResults ) )
        {
            return $this->nbResults;
        }
        $structure = clone $this->query;
        $structure->setLimit( 0 );
        return $this->nbResults = $this->helper->search( $structure )->getResultTotalCount();
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice( $offset, $length )
    {
        $structure = clone $this->query;
        $structure->setOffset( $offset );
        $structure->setLimit( $length );

        $searchResult = $this->helper->search( $structure );
        if ( !isset( $this->nbResults ) )
        {
            $this->nbResults = $searchResult->getResultTotalCount();
        }

        return $searchResult;
    }
}
