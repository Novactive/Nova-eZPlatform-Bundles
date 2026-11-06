<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Item\ValueTransformer;

use Psr\Container\ContainerInterface;

class ItemValueTransformerRegistry
{
    public function __construct(
        protected ContainerInterface $typeContainer
    ) {
    }

    public function get(?string $type): callable
    {
        if (null === $type || !$this->typeContainer->has($type)) {
            return static function ($value) {
                return $value;
            };
        }

        return $this->typeContainer->get($type);
    }
}
