<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Api;

use Ibexa\Solr\Gateway\Endpoint;

class AdminEndpoint extends Endpoint
{
    protected string $distributionStrategyIdentifier;

    /**
     * {@inheritDoc}
     */
    public function getIdentifier(): string
    {
        $authorization = (!empty($this->user) ? "{$this->user}:{$this->pass}@" : '');

        if ('cloud' === $this->distributionStrategyIdentifier) {
            return "{$authorization}{$this->host}:{$this->port}{$this->path}/admin/collections?name={$this->core}";
        }

        return "{$authorization}{$this->host}:{$this->port}{$this->path}/admin/cores?core={$this->core}";
    }
}
