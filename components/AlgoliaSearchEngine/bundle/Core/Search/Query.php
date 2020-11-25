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

use Symfony\Component\Serializer\Annotation as Serialization;

class Query
{
    /**
     * @var string
     * @Serialization\Groups({"Default"})
     */
    private $language;

    /**
     * @var string|null
     * @Serialization\Groups({"Default"})
     */
    private $replica;

    /**
     * @var string
     * @Serialization\Groups({"Default"})
     */
    private $term;

    /**
     * @var array
     */
    private $filters;

    /**
     * @var int
     * @Serialization\Groups({"Default"})
     */
    private $page;

    /**
     * @var int
     * @Serialization\Groups({"Default"})
     */
    private $hitsPerPage;

    /**
     * @var array
     */
    private $facets;

    /**
     * @var array
     */
    private $requestOptions;

    private const DEFAULT_FILTERS = 'doc_type_s:"location"';

    private const DEFAULT_FACETS = ['content_type_name_s', 'section_name_s'];

    public function __construct(
        string $language,
        string $term = '',
        string $filter = '',
        array $facets = [],
        int $page = 0,
        int $hitsPerPage = 25
    ) {
        $this->language = $language;
        $this->term = $term;
        $this->page = $page;
        $this->hitsPerPage = $hitsPerPage;
        $this->filters = [];
        $this->addFilter($filter);
        $this->facets = $facets;
        $this->requestOptions = [];
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    public function getReplica(): ?string
    {
        return $this->replica;
    }

    /**
     * @param string $replica (example: sort_by_content_id_i_desc)
     */
    public function setReplica(string $replica): void
    {
        $this->replica = $replica;
    }

    /**
     * @param string $attribute (example: content_id_i)
     */
    public function setReplicaByAttribute(string $attribute, string $order = 'asc'): void
    {
        $this->replica = "sort_by_{$attribute}_{$order}";
    }

    public function getTerm(): string
    {
        return $this->term;
    }

    public function setTerm(string $term): void
    {
        $this->term = $term;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function setFilters(array $filters): void
    {
        $this->filters = $filters;
    }

    /**
     * @Serialization\Groups({"Default"})
     */
    public function getFiltersString(): string
    {
        if (0 === count($this->filters)) {
            return self::DEFAULT_FILTERS;
        }

        return implode(' AND ', $this->filters);
    }

    public function addFilter(string $filter): self
    {
        $filter = trim($filter);
        if ('' === $filter) {
            return $this;
        }
        $this->filters[] = "({$filter})";

        return $this;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    public function getHitsPerPage(): int
    {
        return $this->hitsPerPage;
    }

    public function setHitsPerPage(int $hitsPerPage): void
    {
        $this->hitsPerPage = $hitsPerPage;
    }

    /**
     * @Serialization\Groups({"Default"})
     */
    public function getFacets(): array
    {
        if (\count($this->facets) > 0) {
            return $this->facets;
        }

        return self::DEFAULT_FACETS;
    }

    public function setFacets(array $facets): void
    {
        $this->facets = $facets;
    }

    public function addFacet(string $facet): void
    {
        $this->facets[] = $facet;
    }

    public function setRequestOption(string $key, $value): void
    {
        $this->requestOptions[$key] = $value;
    }

    public function getRequestOption(string $key)
    {
        return $this->requestOptions[$key];
    }

    /**
     * @Serialization\Groups({"Default"})
     */
    public function getRequestOptions(): array
    {
        return $this->requestOptions;
    }
}
