<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reference;

use AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\Source;

class ReferenceSource extends Source
{
    protected int $scope = Reference::SCOPE_ITEM;

    public function __construct($path, array $transformers = [], int $scope = Reference::SCOPE_ITEM)
    {
        $this->scope = $scope;
        parent::__construct($path, $transformers);
    }

    public function getScope(): int
    {
        return $this->scope;
    }
}
