<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Api;

use Ibexa\Solr\Gateway\Endpoint;

class AdminEndpoint extends Endpoint
{
    /**
     * {@inheritDoc}
     */
    public function getIdentifier(): string
    {
        $authorization = (!empty($this->user) ? "{$this->user}:{$this->pass}@" : '');

        return "{$authorization}{$this->host}:{$this->port}{$this->path}/admin/cores?core={$this->core}";
    }
}
