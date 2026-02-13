<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\Utils;

use AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer\AbstractItemValueTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SprintfTransformer extends AbstractItemValueTransformer
{
    /**
     * @param array $value
     *
     * @return string
     */
    public function transform($value, array $options = [])
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        return sprintf($options['format'], ...$value);
    }

    protected function configureOptions(OptionsResolver $optionsResolver)
    {
        parent::configureOptions($optionsResolver);
        $optionsResolver->define('format')
            ->required()
            ->allowedTypes('string');
    }
}
