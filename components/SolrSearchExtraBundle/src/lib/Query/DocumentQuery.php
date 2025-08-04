<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;

class DocumentQuery extends Query
{
    public string $documentType = 'document';
}
