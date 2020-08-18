<?php
/**
 * NovaeZExtraBundle Result
 *
 * @package   Novactive\Bundle\eZExtraBundle
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */
namespace Novactive\Bundle\eZExtraBundle\Core\Helper\eZ;

use Novactive\Collection\Collection;
use Novactive\Collection\Factory;

/**
 * Class Result
 */
class Result extends Collection
{
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
        parent::__construct([]);
        $this->resultTotalCount = 0;
        $this->resultLimit      = 0;
    }

    /**
     * Set the Total
     *
     * @param integer $resultCount
     *
     * @return $this
     */
    public function setResultTotalCount($resultCount)
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
    public function setResults($results)
    {
        $this->items = Factory::getArrayForItems($results);;

        return $this;
    }

    /**
     * Add a result
     *
     * @param mixed $result
     *
     * @return $this
     */
    public function addResult($result)
    {
        $this->add($result);

        return $this;
    }

    /**
     * Set extras data
     *
     * @param mixed $extras
     *
     * @return $this
     */
    public function setExtras($extras)
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
        return $this->toArray();
    }

    /**
     * Set the Result Limit
     *
     * @param integer $resultLimit
     *
     * @return $this
     */
    public function setResultLimit($resultLimit)
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
    public function setResultOffset($resultOffset)
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
            (($this->getResultLimit() == 0) && ($this->count() < $this->getResultTotalCount())) ||
            (($this->getResultLimit() > 0) && ($this->count() == $this->getResultLimit()) &&
             ($this->getResultLimit() < $this->getResultTotalCount()))
        );

    }

    /**
     * Get the ResultOffsetPage
     *
     * @return int
     */
    public function getResultOffsetPage()
    {
        return intval(ceil($this->getResultOffset() / $this->getResultLimit())) + 1;

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
        return max(1, intval(ceil($this->getResultTotalCount() / $this->getResultLimit())));
    }
}
