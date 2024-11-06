<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\Taxonomy;

use AlmaviaCX\Bundle\IbexaImportExport\Accessor\AbstractItemAccessor;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Symfony\Component\VarExporter\LazyGhostTrait;

class TaxonomyAccessor extends AbstractItemAccessor
{
    use LazyGhostTrait;

    public int $id;
    public string $identifier;
    public string $name;
    /** @var array<string> */
    public array $names;
    public ?TaxonomyAccessor $parent;
    public string $taxonomy;

    protected TaxonomyEntry $taxonomyEntry;

    public function getTaxonomyEntry(): TaxonomyEntry
    {
        return $this->taxonomyEntry;
    }
}
