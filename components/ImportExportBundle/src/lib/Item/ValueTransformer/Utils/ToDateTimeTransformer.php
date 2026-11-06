<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\Utils;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\AbstractItemValueTransformer;
use DateTime;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Transforms a string to a DateTime object.
 * Accept an 'input_format' option to specify the format of the input value.
 */
class ToDateTimeTransformer extends AbstractItemValueTransformer
{
    protected function transform(mixed $value, array $options = []): ?DateTime
    {
        if (empty($value)) {
            return null;
        }

        return DateTime::createFromFormat($options['input_format'], (string) $value);
    }

    protected function configureOptions(OptionsResolver $optionsResolver): void
    {
        parent::configureOptions($optionsResolver);
        $optionsResolver->define('input_format')
                        ->default('Y-m-d')
                        ->allowedTypes('string');
    }
}
