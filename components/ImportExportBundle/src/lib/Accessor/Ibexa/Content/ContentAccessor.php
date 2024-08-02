<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\Content;

use AlmaviaCX\Bundle\IbexaImportExport\Accessor\AbstractItemAccessor;
use AlmaviaCX\Bundle\IbexaImportExport\Accessor\DatetimeAccessor;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Symfony\Component\VarExporter\LazyGhostTrait;

class ContentAccessor extends AbstractItemAccessor
{
    use LazyGhostTrait;

    /** @var \AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\Content\Field\ContentFieldAccessor[] */
    public array $fields;
    /** @var array<string> */
    public array $names;
    public DatetimeAccessor $creationDate;
    public int $mainLocationId;
    public int $id;

    protected Content $content;

    public function getContent(): Content
    {
        return $this->content;
    }
}
