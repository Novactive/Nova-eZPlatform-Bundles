<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\Ibexa;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\AbstractItemValueTransformer;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Value;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Transform a TaxonomyEntry or and array of TaxonomyEntry to a TaxonomyEntryAssignment Value.
 */
class TaxonomyEntryAssignementTransformer extends AbstractItemValueTransformer
{
    protected function transform(mixed $value, array $options = []): Value
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        $entries = array_filter($value, function ($entry) {
            return $entry instanceof TaxonomyEntry;
        });

        return new Value($entries, $options['taxonomy']);
    }

    protected function configureOptions(OptionsResolver $optionsResolver): void
    {
        parent::configureOptions($optionsResolver);
        $optionsResolver->define('taxonomy')
                        ->required()
                        ->allowedTypes('string');
    }
}
