<?php

/**
 * NovaeZSolrSearchExtraBundle.
 *
 * @package   NovaeZSolrSearchExtraBundle
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZSolrSearchExtraBundle/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Api\Schema\Analysis\Synonyms;

class SynonymsMap
{
    /** @var string */
    protected $term;

    /** @var string[] */
    protected $synonyms;

    /**
     * SynonymsMapping constructor.
     *
     * @param string[] $synonyms
     */
    public function __construct(string $term, array $synonyms)
    {
        $this->term     = $term;
        $this->synonyms = $synonyms;
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
