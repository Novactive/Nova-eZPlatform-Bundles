<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\Utils;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\AbstractItemValueTransformer;
use InvalidArgumentException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Transforms an array of values into a single string by joining them.
 * Accept a 'separator' option to specify the separator string.
 */
class JoinTransformer extends AbstractItemValueTransformer
{
    protected function transform(mixed $value, array $options = []): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!is_array($value)) {
            throw new InvalidArgumentException('Value must be an array.');
        }

        return implode($options['separator'], $value);
    }

    protected function configureOptions(OptionsResolver $optionsResolver): void
    {
        parent::configureOptions($optionsResolver);
        $optionsResolver->define('separator')
            ->default(',')
            ->allowedTypes('string');
    }
}
