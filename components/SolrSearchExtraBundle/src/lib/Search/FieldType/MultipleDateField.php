<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Search\FieldType;

use Ibexa\Contracts\Core\Search\FieldType;

class MultipleDateField extends FieldType
{
    /**
     * The type name of the facet. Has to be handled by the solr schema.
     *
     * @var string
     */
    protected $type = 'ez_mdate';
}
