<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;

class AdvancedContentQuery extends Query
{
    /** @var array */
    public $groupConfig;
}
