<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Component;

use InvalidArgumentException;
use Psr\Log\LoggerAwareTrait;

abstract class AbstractComponent implements ComponentInterface
{
    use LoggerAwareTrait;

    protected ComponentOptions $options;

    public static function getOptionsFormType(): ?string
    {
        return null;
    }

    public static function getOptionsType(): ?string
    {
        return ComponentOptions::class;
    }

    /**
     * @param \AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentOptions $options
     */
    public function setOptions(ComponentOptions $options): void
    {
        $requiredOptionType = static::getOptionsType();
        if (!$options instanceof $requiredOptionType) {
            throw new InvalidArgumentException('Options must be an instance of '.$requiredOptionType);
        }
        $this->options = $options;
    }

    public function getOptions(): ComponentOptions
    {
        return $this->options;
    }

    public function getOption(string $name, $default = null)
    {
        return $this->options->{$name} ?? $default;
    }

    public function clean(): void
    {
    }
}
