<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Component;

use InvalidArgumentException;

class ComponentReference
{
    public function __construct(
        protected string $type,
        protected ?ComponentOptions $options = null,
        protected int $priority = 0
    ) {
        $requiredOptionsType = call_user_func([$type, 'getOptionsType']);
        if (!$options && $requiredOptionsType) {
            $options = new $requiredOptionsType();
        }
        if (!$options instanceof $requiredOptionsType) {
            throw new InvalidArgumentException('Options must be an instance of '.$requiredOptionsType);
        }
        $this->options = $options;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getOptions(): ?ComponentOptions
    {
        return $this->options;
    }

    /**
     * @param \AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentOptions|null $options
     */
    public function setOptions(?ComponentOptions $options): void
    {
        $this->options = $options;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }
}
