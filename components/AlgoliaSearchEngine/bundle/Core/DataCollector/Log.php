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

namespace Novactive\Bundle\eZAlgoliaSearchEngine\Core\DataCollector;

class Log
{
    /**
     * @var string
     */
    private $mode;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $languageCode;

    /**
     * @var string
     */
    private $indexName;

    /**
     * @var string
     */
    private $query;

    /**
     * @var array
     */
    private $requestOptions;

    /**
     * @var array
     */
    private $results;

    /**
     * @var array
     */
    private $objects;

    /**
     * @var float
     */
    private $executionTime;

    public function fromSearch(
        string $mode,
        string $languageCode,
        string $indexName,
        string $query,
        array $requestOptions,
        array $results
    ): void {
        $this->mode = $mode;
        $this->method = 'search';
        $this->languageCode = $languageCode;
        $this->indexName = $indexName;
        $this->query = $query;
        $this->requestOptions = $requestOptions;
        $this->results = $results;
    }

    public function fromSave(
        string $mode,
        string $languageCode,
        string $indexName,
        array $objects
    ): void {
        $this->mode = $mode;
        $this->method = 'save';
        $this->languageCode = $languageCode;
        $this->indexName = $indexName;
        $this->objects = $objects;
    }

    public function fromDelete(
        string $mode,
        string $languageCode,
        string $indexName,
        array $objects
    ): void {
        $this->mode = $mode;
        $this->method = 'delete';
        $this->languageCode = $languageCode;
        $this->indexName = $indexName;
        $this->objects = $objects;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    public function setLanguageCode(string $languageCode): void
    {
        $this->languageCode = $languageCode;
    }

    public function getIndexName(): string
    {
        return $this->indexName;
    }

    public function setIndexName(string $indexName): void
    {
        $this->indexName = $indexName;
    }

    public function getQuery(): string
    {
        return $this->query ?? '';
    }

    public function setQuery(string $query): void
    {
        $this->query = $query;
    }

    public function getRequestOptions(): array
    {
        return $this->requestOptions ?? [];
    }

    public function setRequestOptions(array $requestOptions): void
    {
        $this->requestOptions = $requestOptions;
    }

    public function getResults(): array
    {
        return $this->results ?? [];
    }

    public function setResults(array $results): void
    {
        $this->results = $results;
    }

    public function getObjects(): array
    {
        return $this->objects ?? [];
    }

    public function setObjects(array $objects): void
    {
        $this->objects = $objects;
    }

    public function getExecutionTime(): float
    {
        return $this->executionTime;
    }

    public function setExecutionTime(float $executionTime): void
    {
        $this->executionTime = $executionTime;
    }
}
