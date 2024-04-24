<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\Utils;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\AbstractItemValueTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JoinTransformer extends AbstractItemValueTransformer
{
    /**
     * @param array $value
     *
     * @return string
     */
    public function transform($value, array $options = [])
    {
        if (null === $value) {
            return null;
        }

        return implode($options['separator'], $value);
    }

    protected function configureOptions(OptionsResolver $optionsResolver)
    {
        parent::configureOptions($optionsResolver);
        $optionsResolver->define('separator')
            ->default(',')
            ->allowedTypes('string');
    }
}
