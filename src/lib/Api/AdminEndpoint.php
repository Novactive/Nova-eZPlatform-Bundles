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

namespace Novactive\EzSolrSearchExtra\Api;

use EzSystems\EzPlatformSolrSearchEngine\Gateway\Endpoint;

class AdminEndpoint extends Endpoint
{
    /**
     * @inheritDoc
     */
    public function getIdentifier()
    {
        $authorization = (!empty($this->user) ? "{$this->user}:{$this->pass}@" : '');

        return "{$authorization}{$this->host}:{$this->port}{$this->path}/admin/cores?core={$this->core}";
    }
}
