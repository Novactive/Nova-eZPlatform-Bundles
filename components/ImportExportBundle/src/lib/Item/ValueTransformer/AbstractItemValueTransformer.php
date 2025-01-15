<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer;

use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractItemValueTransformer implements ItemValueTransformerInterface
{
    public function __invoke($value, array $options = [])
    {
        $options = $this->resolveOptions($options);

        return $this->transform($value, $options);
    }

    abstract protected function transform($value, array $options = []);

    protected function resolveOptions(array $options): array
    {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);

        return $optionsResolver->resolve($options);
    }

    protected function configureOptions(OptionsResolver $optionsResolver)
    {
    }
}
