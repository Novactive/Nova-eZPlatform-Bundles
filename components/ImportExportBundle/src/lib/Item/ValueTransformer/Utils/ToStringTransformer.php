<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\Utils;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\AbstractItemValueTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Transforms a value to its string representation.
 */
class ToStringTransformer extends AbstractItemValueTransformer
{
    protected function transform(mixed $value, array $options = []): string
    {
        $value = (string) $value;
        if (null !== $options['max_length'] && strlen($value) > $options['max_length']) {
            return substr($value, 0, $options['max_length']);
        }

        return $value;
    }

    protected function configureOptions(OptionsResolver $optionsResolver): void
    {
        parent::configureOptions($optionsResolver);
        $optionsResolver->define('max_length')
                        ->default(null)
                        ->allowedTypes('integer', 'null');
    }
}
