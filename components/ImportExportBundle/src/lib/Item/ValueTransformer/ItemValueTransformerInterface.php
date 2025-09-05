<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer;

interface ItemValueTransformerInterface
{
    public function __invoke($value, array $options = []);
}
