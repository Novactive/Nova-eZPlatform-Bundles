<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\Utils;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\AbstractItemValueTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Transforms a value using sprintf.
 * Accept a 'format' option to specify the format string.
 *
 * @see https://www.php.net/manual/en/function.sprintf.php
 */
class SprintfTransformer extends AbstractItemValueTransformer
{
    protected function transform(mixed $value, array $options = []): ?string
    {
        if (null == $value) {
            return null;
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        return sprintf($options['format'], ...$value);
    }

    protected function configureOptions(OptionsResolver $optionsResolver): void
    {
        parent::configureOptions($optionsResolver);
        $optionsResolver->define('format')
            ->required()
            ->allowedTypes('string');
    }
}
