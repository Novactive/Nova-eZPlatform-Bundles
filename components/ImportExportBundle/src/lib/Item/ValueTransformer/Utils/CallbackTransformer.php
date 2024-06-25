<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\Utils;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\AbstractItemValueTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CallbackTransformer extends AbstractItemValueTransformer
{
    protected function transform($value, array $options = [])
    {
        return call_user_func($options['callback'], $value);
    }

    protected function configureOptions(OptionsResolver $optionsResolver)
    {
        parent::configureOptions($optionsResolver);
        $optionsResolver->define('callback')
                        ->required()
                        ->allowedTypes('callable', 'array');
    }
}
