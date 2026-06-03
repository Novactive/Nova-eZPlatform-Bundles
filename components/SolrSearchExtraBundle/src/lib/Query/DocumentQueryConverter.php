<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Solr\Query\Common\QueryConverter\NativeQueryConverter;
use Override;

class DocumentQueryConverter extends NativeQueryConverter
{
    /**
     * @param DocumentQuery $query
     */
    #[Override]
    public function convert(Query $query, array $languageSettings = []): array
    {
        $params = parent::convert($query, $languageSettings);

        return array_merge(
            $params,
            $query->rawParams
        );
    }
}
