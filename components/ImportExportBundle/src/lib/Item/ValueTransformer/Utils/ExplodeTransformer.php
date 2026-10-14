<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\Utils;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\AbstractItemValueTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Transforms a string value into an array by splitting it.
 * Accept a 'separator' option to specify the delimiter.
 */
class ExplodeTransformer extends AbstractItemValueTransformer
{
    /**
     * @return string[]|null
     */
    protected function transform(mixed $value, array $options = [])
    {
        if (null === $value) {
            return null;
        }

        return explode($options['separator'], $value);
    }

    protected function configureOptions(OptionsResolver $optionsResolver): void
    {
        parent::configureOptions($optionsResolver);
        $optionsResolver->define('separator')
                        ->default(',')
                        ->allowedTypes('string');
    }
}
