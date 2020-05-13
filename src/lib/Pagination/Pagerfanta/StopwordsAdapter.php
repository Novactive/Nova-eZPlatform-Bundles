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

use Novactive\EzSolrSearchExtra\Api\Schema\Analysis\Stopwords\StopwordsService;
use Pagerfanta\Adapter\AdapterInterface;

class StopwordsAdapter implements AdapterInterface
{
    /** @var string */
    private $setId;

    /** @var StopwordsService */
    private $stopwordsService;

    /** @var int */
    private $nbResults;

    /**
     * StopwordsAdapter constructor.
     */
    public function __construct(string $setId, StopwordsService $stopwordsService)
    {
        $this->setId            = $setId;
        $this->stopwordsService = $stopwordsService;
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
        $resuls = $this->stopwordsService->getWords($this->setId, $offset, $length);
        if (!isset($this->nbResults)) {
            $this->nbResults = count($resuls);
        }

        return $resuls;
    }
}
