<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\Content\Field\ValueTransformer;

use AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\Taxonomy\TaxonomyAccessorBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Value as TaxonomyEntryAssignmentValue;

class TaxonomyFieldValueTransformer implements FieldValueTransformerInterface
{
    protected TaxonomyAccessorBuilder $taxonomyAccessorBuilder;

    public function __construct(TaxonomyAccessorBuilder $taxonomyAccessorBuilder)
    {
        $this->taxonomyAccessorBuilder = $taxonomyAccessorBuilder;
    }

    /**
     * @return array<\Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry>
     */
    public function __invoke(Field $field, FieldDefinition $fieldDefinition): array
    {
        /** @var TaxonomyEntryAssignmentValue $fieldValue */
        $fieldValue = $field->value;

        return array_map(function (TaxonomyEntry $taxonomy) {
            return $this->taxonomyAccessorBuilder->buildFromTaxonomyEntry($taxonomy);
        }, $fieldValue->getTaxonomyEntries());
    }
}
