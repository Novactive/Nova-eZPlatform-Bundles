<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Values;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Override;
use stdClass;

/**
 * Wraps a raw Solr document (stdClass) as a ValueObject.
 * Proxies property access to the underlying stdClass for backward compatibility.
 */
class DocumentHit extends ValueObject
{
    public function __construct(public stdClass $document)
    {
        parent::__construct();
    }

    #[Override]
    public function __get($property)
    {
        if (property_exists($this->document, $property)) {
            return $this->document->$property;
        }

        return parent::__get($property);
    }

    #[Override]
    public function __isset($property)
    {
        if (property_exists($this->document, $property)) {
            return true;
        }

        return parent::__isset($property);
    }
}
