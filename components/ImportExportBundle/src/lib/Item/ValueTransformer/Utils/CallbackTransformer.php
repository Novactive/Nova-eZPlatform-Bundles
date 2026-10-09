<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\Utils;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\AbstractItemValueTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Transforms a value using a callback function.
 * Accept a 'callback' option to specify the function to be called.
 */
class CallbackTransformer extends AbstractItemValueTransformer
{
    /**
     * @return mixed
     */
    protected function transform(mixed $value, array $options = [])
    {
        return call_user_func($options['callback'], $value);
    }

    protected function configureOptions(OptionsResolver $optionsResolver): void
    {
        parent::configureOptions($optionsResolver);
        $optionsResolver->define('callback')
                        ->required()
                        ->allowedTypes('callable', 'array');
    }
}
