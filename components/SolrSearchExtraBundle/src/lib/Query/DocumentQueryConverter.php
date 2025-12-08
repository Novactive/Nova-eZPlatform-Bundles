<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Solr\Query\Common\QueryConverter\NativeQueryConverter;

class DocumentQueryConverter extends NativeQueryConverter
{
    /**
     * @param \Novactive\EzSolrSearchExtra\Query\DocumentQuery $query
     */
    public function convert(Query $query, array $languageSettings = [])
    {
        $params = parent::convert($query, $languageSettings);

        return array_merge(
            $params,
            $query->rawParams
        );
    }
}
