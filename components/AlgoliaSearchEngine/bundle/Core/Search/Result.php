<?php

/**
 * Nova eZ Algolia Search Engine.
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @licence   "SEE FULL LICENSE OPTIONS IN LICENSE.md"
 *            Nova eZ Algolia Search Engine is tri-licensed, meaning you must choose one of three licenses to use:
 *                - Commercial License: a paid license, meant for commercial use. The default option for most users.
 *                - Creative Commons Non-Commercial No-Derivatives: meant for trial and non-commercial use.
 *                - GPLv3 License: meant for open-source projects.
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZAlgoliaSearchEngine\Core\Search;

use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use LogicException;

class Result implements ArrayAccess, IteratorAggregate
{
    /**
     * @var int
     */
    public $totalHits;

    /**
     * @var int
     */
    public $totalPages;

    /**
     * @var int
     */
    public $page;

    /**
     * @var int
     */
    public $limit;

    /**
     * @var array
     */
    private $hits;

    /**
     * @var array
     */
    private $facets;

    public function __construct(int $totalHits, int $totalPages, int $page, int $limit, array $hits)
    {
        $this->totalHits = $totalHits;
        $this->totalPages = $totalPages;
        $this->page = $page;
        $this->limit = $limit;
        $this->hits = $hits;
        $this->facets = [];
    }

    public static function createFromResponse(array $response): self
    {
        $result = new static(
            $response['nbHits'],
            $response['nbPages'],
            $response['page'],
            $response['hitsPerPage'],
            $response['hits']
        );

        if (isset($response['facets'])) {
            $result->facets = $response['facets'];
        }

        return $result;
    }

    public function getFacets(string $name): array
    {
        if (!\array_key_exists($name, $this->facets)) {
            return [];
        }

        return $this->facets[$name];
    }

    public function offsetExists($offset): bool
    {
        return isset($this->hits[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->hits[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        throw new LogicException('You can not overwrite the value for offset "'.$offset.'"');
    }

    public function offsetUnset($offset): void
    {
        throw new LogicException('You can not overwrite the value for offset "'.$offset.'"');
    }

    public function getIterator()
    {
        return new ArrayIterator($this->hits);
    }

    public function __set($name, $value): void
    {
        throw new LogicException('You can not overwrite the value for property "'.$name.'"');
    }
}
