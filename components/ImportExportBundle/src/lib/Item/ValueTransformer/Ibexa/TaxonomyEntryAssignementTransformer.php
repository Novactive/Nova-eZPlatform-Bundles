<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\Ibexa;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\AbstractItemValueTransformer;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Value;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaxonomyEntryAssignementTransformer extends AbstractItemValueTransformer
{
    /**
     * @param TaxonomyEntry|TaxonomyEntry[] $value
     *
     * @return \Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Value
     */
    public function transform($value, array $options = [])
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        return new Value(array_filter($value), $options['taxonomy']);
    }

    protected function configureOptions(OptionsResolver $optionsResolver)
    {
        parent::configureOptions($optionsResolver);
        $optionsResolver->define('taxonomy')
                        ->required()
                        ->allowedTypes('string');
    }
}
