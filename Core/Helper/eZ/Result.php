<?php
/**
 * NovaeZExtraBundle Result
 *
 * @package   Novactive\Bundle\eZExtraBundle
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZSEOBundle/blob/master/LICENSE MIT Licence
 */
namespace Novactive\Bundle\eZExtraBundle\Core\Helper\eZ;

/**
 * Class Result
 */
class Result implements \Iterator, \ArrayAccess
{
    /**
     * Seek position
     *
     * @var integer
     */
    protected $seek;

    /**
     * Result array
     *
     * @var array
     */
    protected $results;

    /**
     * Extra data
     *
     * @var array
     */
    protected $extras;

    /**
     * Total
     *
     * @var integer
     */
    protected $resultTotalCount;

    /**
     * Limit
     *
     * @var integer
     */
    protected $resultLimit;

    /**
     * Offset
     *
     * @var integer
     */
    protected $resultOffset;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->resultTotalCount = 0;
        $this->seek             = 0;
        $this->result           = [];
        $this->facets           = [];
        $this->resultLimit      = 0;
        $this->hasMore          = true;
    }

    /**
     * Set the Total
     *
     * @param integer $resultCount
     *
     * @return $this
     */
    public function setResultTotalCount( $resultCount )
    {
        $this->resultTotalCount = $resultCount;

        return $this;
    }

    /**
     * Return tht Total
     *
     * @return integer
     */
    public function getResultTotalCount()
    {
        return $this->resultTotalCount;
    }

    /**
     * Set the Results
     *
     * @param mixed $results
     *
     * @return $this
     */
    public function setResults( $results )
    {
        $this->results = $results;

        return $this;
    }

    /**
     * Add a result
     *
     * @param mixed $result
     *
     * @return $this
     */
    public function addResult( $result )
    {
        $this->results[] = $result;

        return $this;
    }

    /**
     * Set extras data
     *
     * @param mixed $extras
     *
     * @return $this
     */
    public function setExtras( $extras )
    {
        $this->extras = $extras;

        return $this;
    }

    /**
     * Get Extras
     *
     * @return mixed
     */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
     * Get Results
     *
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * Set the Result Limit
     *
     * @param integer $resultLimit
     *
     * @return $this
     */
    public function setResultLimit( $resultLimit )
    {
        $this->resultLimit = $resultLimit;

        return $this;
    }

    /**
     * Get the Result Limit
     *
     * @return integer
     */
    public function getResultLimit()
    {
        return $this->resultLimit;
    }

    /**
     * Set the Result Offset
     *
     * @param integer $resultOffset
     *
     * @return $this
     */
    public function setResultOffset( $resultOffset )
    {
        $this->resultOffset = $resultOffset;

        return $this;
    }

    /**
     * Get the result offset
     *
     * @return mixed
     */
    public function getResultOffset()
    {
        return $this->resultOffset;
    }

    /**
     * Has More ?
     *
     * @return bool
     */
    public function hasMore()
    {
        return (
            ( ( $this->getResultLimit() == 0 ) && ( $this->count() < $this->getResultTotalCount() ) ) ||
            ( ( $this->getResultLimit() > 0 ) && ( $this->count() == $this->getResultLimit() ) &&
              ( $this->getResultLimit() < $this->getResultTotalCount() ) )
        );

    }

    /**
     * Get the ResultOffsetPage
     *
     * @return int
     */
    public function getResultOffsetPage()
    {
        return intval( ceil( $this->getResultOffset() / $this->getResultLimit() ) ) + 1;

    }

    /**
     * Get the Next page
     *
     * @return bool|int
     */
    public function nextPage()
    {
        return $this->getResultOffsetPage() < $this->getLastPage() ? $this->getResultOffsetPage() + 1 : false;
    }

    /**
     * Get the Previous page
     *
     * @return bool|int
     */
    public function previousPage()
    {
        return $this->getResultOffsetPage() > 0 ? $this->getResultOffsetPage() - 1 : false;
    }

    /**
     * Get the Last page
     *
     * @return bool|int
     */
    public function getLastPage()
    {
        return max( 1, intval( ceil( $this->getResultTotalCount() / $this->getResultLimit() ) ) );
    }

    /**
     * Count
     *
     * @return bool|int
     */
    public function count()
    {
        return count( $this->results );
    }

    /**
     * Return the current element
     *
     * @link http://php.net/manual/en/iterator.current.php
     *
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->results[$this->seek];
    }

    /**
     * Move forward to next element
     *
     * @link http://php.net/manual/en/iterator.next.php
     *
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        ++$this->seek;
    }

    /**
     * Return the key of the current element
     *
     * @link http://php.net/manual/en/iterator.key.php
     *
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->seek;
    }

    /**
     * Checks if current position is valid
     *
     * @link http://php.net/manual/en/iterator.valid.php
     *
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return isset( $this->results[$this->seek] );
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     *
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->seek = 0;
    }

    /**
     * Whether a offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     *
     * @return boolean true on success or false on failure.
     *                      </p>
     *                      <p>
     *                      The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists( $offset )
    {
        return isset( $this->results[$offset] );
    }

    /**
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet( $offset )
    {
        return isset( $this->results[$offset] ) ? $this->results[$offset] : null;
    }

    /**
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     *
     * @return void
     */
    public function offsetSet( $offset, $value )
    {
        if ( is_null( $offset ) )
        {
            $this->results[] = $value;
        }
        else
        {
            $this->results[$offset] = $value;
        }
    }

    /**
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     *
     * @return void
     */
    public function offsetUnset( $offset )
    {
        unset( $this->results[$offset] );
    }
}
