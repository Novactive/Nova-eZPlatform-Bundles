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

declare(strict_types=1);

namespace Novactive\Bundle\eZExtraBundle\Core\Helper\eZ;

use Novactive\Collection\Collection;
use Novactive\Collection\Factory;

class Result extends Collection
{
    /**
     * @var array
     */
    protected $extras;

    /**
     * @var int
     */
    protected $resultTotalCount;

    /**
     * @var int
     */
    protected $resultLimit;

    /**
     * @var int
     */
    protected $resultOffset;

    public function __construct()
    {
        parent::__construct([]);
        $this->resultTotalCount = 0;
        $this->resultLimit = 0;
    }

    public function setResultTotalCount(int $resultCount): self
    {
        $this->resultTotalCount = $resultCount;

        return $this;
    }

    public function getResultTotalCount(): int
    {
        return $this->resultTotalCount;
    }

    public function setResults($results): self
    {
        $this->items = Factory::getArrayForItems($results);

        return $this;
    }

    public function addResult($result): self
    {
        $this->add($result);

        return $this;
    }

    public function setExtras(array $extras): self
    {
        $this->extras = $extras;

        return $this;
    }

    public function getExtras(): array
    {
        return $this->extras;
    }

    public function getResults(): array
    {
        return $this->toArray();
    }

    public function setResultLimit(int $resultLimit): self
    {
        $this->resultLimit = $resultLimit;

        return $this;
    }

    public function getResultLimit(): int
    {
        return $this->resultLimit;
    }

    public function setResultOffset(int $resultOffset): self
    {
        $this->resultOffset = $resultOffset;

        return $this;
    }

    public function getResultOffset(): int
    {
        return $this->resultOffset;
    }

    public function hasMore(): bool
    {
        return
            ((0 === $this->getResultLimit()) && ($this->count() < $this->getResultTotalCount())) ||
            (($this->getResultLimit() > 0) && ($this->count() === $this->getResultLimit()) &&
             ($this->getResultLimit() < $this->getResultTotalCount()));
    }

    public function getResultOffsetPage(): int
    {
        return (int) ceil($this->getResultOffset() / $this->getResultLimit()) + 1;
    }

    /**
     * @return bool|int
     */
    public function nextPage()
    {
        return $this->getResultOffsetPage() < $this->getLastPage() ? $this->getResultOffsetPage() + 1 : false;
    }

    /**
     * @return bool|int
     */
    public function previousPage()
    {
        return $this->getResultOffsetPage() > 0 ? $this->getResultOffsetPage() - 1 : false;
    }

    /**
     * @return bool|int
     */
    public function getLastPage()
    {
        return max(1, (int) ceil($this->getResultTotalCount() / $this->getResultLimit()));
    }
}
