<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Component;

class ComponentReference
{
    protected string $type;
    protected ?ComponentOptions $options = null;

    public function __construct(string $type, ?ComponentOptions $options = null)
    {
        $this->type = $type;
        $this->options = $options;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getOptions(): ?ComponentOptions
    {
        return $this->options;
    }
}
