<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer;

use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractItemValueTransformer implements ItemValueTransformerInterface
{
    public function __invoke(mixed $value, array $options = [])
    {
        $options = $this->resolveOptions($options);

        return $this->transform($value, $options);
    }

    /**
     * @param array<string, mixed> $options*
     *
     * @return mixed
     */
    abstract protected function transform(mixed $value, array $options = []);

    /**
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    protected function resolveOptions(array $options): array
    {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);

        return $optionsResolver->resolve($options);
    }

    protected function configureOptions(OptionsResolver $optionsResolver): void
    {
    }
}
