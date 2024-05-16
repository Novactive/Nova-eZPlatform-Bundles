<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Api\Schema\Analysis\Synonyms;

class SynonymsMap
{
    /**
     * SynonymsMapping constructor.
     *
     * @param string[] $synonyms
     */
    public function __construct(protected string $term, protected array $synonyms)
    {
    }

    public function getTerm(): string
    {
        return $this->term;
    }

    /**
     * @return string[]
     */
    public function getSynonyms(): array
    {
        return $this->synonyms;
    }
}
