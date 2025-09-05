<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\Taxonomy;

use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;

class TaxonomyAccessorBuilder
{
    public function buildFromTaxonomyEntry(TaxonomyEntry $taxonomyEntry): TaxonomyAccessor
    {
        return $this->create(function () use ($taxonomyEntry) {
            return $taxonomyEntry;
        });
    }

    public function create(callable $taxonomyEntryInitializer): TaxonomyAccessor
    {
        $initializers = [
            "\0*\0taxonomyEntry" => $taxonomyEntryInitializer,
            'id' => function (TaxonomyAccessor $instance) {
                return $instance->getTaxonomyEntry()->getId();
            },
            'identifier' => function (TaxonomyAccessor $instance) {
                return $instance->getTaxonomyEntry()->getIdentifier();
            },
            'name' => function (TaxonomyAccessor $instance) {
                return $instance->getTaxonomyEntry()->getName();
            },
            'names' => function (TaxonomyAccessor $instance) {
                return $instance->getTaxonomyEntry()->getNames();
            },
            'parent' => function (TaxonomyAccessor $instance) {
                $parent = $instance->getTaxonomyEntry()->getParent();

                return $parent ? $this->buildFromTaxonomyEntry($parent) : null;
            },
            'taxonomy' => function (TaxonomyAccessor $instance) {
                return $instance->getTaxonomyEntry()->getTaxonomy();
            },
        ];

        return TaxonomyAccessor::createLazyGhost($initializers);
    }
}
