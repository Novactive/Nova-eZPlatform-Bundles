<?php

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
        $this->setId = $setId;
        $this->synonymsService = $synonymsService;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function getNbResults(): int
    {
        if (isset($this->nbResults)) {
            return $this->nbResults;
        }

        $this->getSlice(0, 0);

        return $this->nbResults;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function getSlice($offset, $length): array
    {
        $resuls = $this->synonymsService->getMappings($this->setId, $offset, $length);
        if (!isset($this->nbResults)) {
            $this->nbResults = count($resuls);
        }

        return $resuls;
    }
}
