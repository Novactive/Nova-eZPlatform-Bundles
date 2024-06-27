<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\Utils;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\AbstractItemValueTransformer;
use DateTime;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ToDateTimeTransformer extends AbstractItemValueTransformer
{
    protected function transform($value, array $options = [])
    {
        if (empty($value)) {
            return null;
        }

        return DateTime::createFromFormat($options['input_format'], $value);
    }

    protected function configureOptions(OptionsResolver $optionsResolver)
    {
        parent::configureOptions($optionsResolver);
        $optionsResolver->define('input_format')
                        ->default('Y-m-d')
                        ->allowedTypes('string');
    }
}
