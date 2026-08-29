<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\Content\Field;

use Symfony\Component\VarExporter\LazyGhostTrait;

class ContentFieldAccessor
{
    use LazyGhostTrait;

    public mixed $value;
}
