<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\Utils;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\AbstractItemValueTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DefaultTransformer extends AbstractItemValueTransformer
{
    protected function transform(mixed $value, array $options = [])
    {
        return $value ?? $options['default_value'];
    }

    protected function configureOptions(OptionsResolver $optionsResolver): void
    {
        parent::configureOptions($optionsResolver);
        $optionsResolver->define('default_value')
                        ->default(null);
    }
}
