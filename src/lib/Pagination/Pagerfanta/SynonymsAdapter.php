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

namespace Novactive\EzSolrSearchExtra\Pagination\Pagerfanta;

use Novactive\EzSolrSearchExtra\Api\Schema\Analysis\Synonyms\SynonymsService;
use Pagerfanta\Adapter\AdapterInterface;

class SynonymsAdapter implements AdapterInterface
{
    /** @var string */
    private $setId;

    /** @var SynonymsService */
    private $synonymsService;

    /** @var int */
    private $nbResults;

    /**
     * SynonymsAdapter constructor.
     */
    public function __construct(string $setId, SynonymsService $synonymsService)
    {
        $this->setId           = $setId;
        $this->synonymsService = $synonymsService;
    }

    /**
     * @inheritDoc
     */
    public function getNbResults()
    {
        if (isset($this->nbResults)) {
            return $this->nbResults;
        }

        $this->getSlice(0, 0);

        return $this->nbResults;
    }

    /**
     * @inheritDoc
     */
    public function getSlice($offset, $length)
    {
        $resuls = $this->synonymsService->getMappings($this->setId, $offset, $length);
        if (!isset($this->nbResults)) {
            $this->nbResults = count($resuls);
        }

        return $resuls;
    }
}
