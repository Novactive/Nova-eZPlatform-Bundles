<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer;

/**
 * @phpstan-type TransformerOptions array<string, mixed>
 */
interface ItemValueTransformerInterface
{
    /**
     * @param TransformerOptions $options
     *
     * @return mixed
     */
    public function __invoke(mixed $value, array $options = []);
}
