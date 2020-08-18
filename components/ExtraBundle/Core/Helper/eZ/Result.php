<?php

/**
 * NovaeZExtraBundle Result.
 *
 * @package   Novactive\Bundle\eZExtraBundle
 *
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */

namespace Novactive\Bundle\eZExtraBundle\Core\Helper\eZ;

use Novactive\Collection\Collection;
use Novactive\Collection\Factory;

/**
 * Class Result.
 */
class Result extends Collection
{
    /**
     * Extra data.
     *
     * @var array
     */
    protected $extras;

    /**
     * Total.
     *
     * @var int
     */
    protected $resultTotalCount;

    /**
     * Limit.
     *
     * @var int
     */
    protected $resultLimit;

    /**
     * Offset.
     *
     * @var int
     */
    protected $resultOffset;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct([]);
        $this->resultTotalCount = 0;
        $this->resultLimit = 0;
    }

    /**
     * Set the Total.
     *
     * @param int $resultCount
     *
     * @return $this
     */
    public function setResultTotalCount($resultCount)
    {
        $this->resultTotalCount = $resultCount;

        return $this;
    }

    /**
     * Return tht Total.
     *
     * @return int
     */
    public function getResultTotalCount()
    {
        return $this->resultTotalCount;
    }

    /**
     * Set the Results.
     *
     * @return $this
     */
    public function setResults($results)
    {
        $this->items = Factory::getArrayForItems($results);

        return $this;
    }

    /**
     * Add a result.
     *
     * @return $this
     */
    public function addResult($result)
    {
        $this->add($result);

        return $this;
    }

    /**
     * Set extras data.
     *
     * @return $this
     */
    public function setExtras($extras)
    {
        $this->extras = $extras;

        return $this;
    }

    /**
     * Get Extras.
     */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
     * Get Results.
     *
     * @return array
     */
    public function getResults()
    {
        return $this->toArray();
    }

    /**
     * Set the Result Limit.
     *
     * @param int $resultLimit
     *
     * @return $this
     */
    public function setResultLimit($resultLimit)
    {
        $this->resultLimit = $resultLimit;

        return $this;
    }

    /**
     * Get the Result Limit.
     *
     * @return int
     */
    public function getResultLimit()
    {
        return $this->resultLimit;
    }

    /**
     * Set the Result Offset.
     *
     * @param int $resultOffset
     *
     * @return $this
     */
    public function setResultOffset($resultOffset)
    {
        $this->resultOffset = $resultOffset;

        return $this;
    }

    /**
     * Get the result offset.
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
        return
            ((0 == $this->getResultLimit()) && ($this->count() < $this->getResultTotalCount())) ||
            (($this->getResultLimit() > 0) && ($this->count() == $this->getResultLimit()) &&
             ($this->getResultLimit() < $this->getResultTotalCount()))
        ;
    }

    /**
     * Get the ResultOffsetPage.
     *
     * @return int
     */
    public function getResultOffsetPage()
    {
        return intval(ceil($this->getResultOffset() / $this->getResultLimit())) + 1;
    }

    /**
     * Get the Next page.
     *
     * @return bool|int
     */
    public function nextPage()
    {
        return $this->getResultOffsetPage() < $this->getLastPage() ? $this->getResultOffsetPage() + 1 : false;
    }

    /**
     * Get the Previous page.
     *
     * @return bool|int
     */
    public function previousPage()
    {
        return $this->getResultOffsetPage() > 0 ? $this->getResultOffsetPage() - 1 : false;
    }

    /**
     * Get the Last page.
     *
     * @return bool|int
     */
    public function getLastPage()
    {
        return max(1, intval(ceil($this->getResultTotalCount() / $this->getResultLimit())));
    }
}
