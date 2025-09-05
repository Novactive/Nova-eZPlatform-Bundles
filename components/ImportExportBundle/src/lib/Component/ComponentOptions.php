<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Component;

use Exception;
use ReflectionClass;

class ComponentOptions
{
    protected array $initializationState = [];

    public function __construct()
    {
        $availableOptions = $this->getAvailableOptions();
        foreach ($availableOptions as $option) {
            $this->initializationState[$option] = false;
        }
    }

    public function getInitializationState(): array
    {
        return $this->initializationState;
    }

    public function getInitializedOptions(): array
    {
        return array_keys(array_filter($this->initializationState, static function ($option) {
            return true === $option;
        }));
    }

    public function getNonInitializedOptions(): array
    {
        return array_keys(array_filter($this->initializationState, static function ($option) {
            return false === $option;
        }));
    }

    public function isOptionInitialised(string $name): bool
    {
        return $this->initializationState[$name] ?? false;
    }

    public function getAvailableOptions(): iterable
    {
        $properties = (new ReflectionClass(static::class))->getProperties();
        foreach ($properties as $property) {
            if ('initializedOptions' === $property->getName()) {
                continue;
            }
            yield $property->getName();
        }
    }

    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->initializationState[$name] = true;
            $this->{$name} = $value;

            return;
        }
        $className = static::class;
        throw new Exception("Option '{$name}' not found on '{$className}'");
    }

    public function __get($name)
    {
        return $this->{$name} ?? null;
    }

    public function merge(ComponentOptions $overrideOptions): ComponentOptions
    {
        $availableOptions = $this->getNonInitializedOptions();
        foreach ($availableOptions as $availableOption) {
            if (!isset($overrideOptions->{$availableOption})) {
                continue;
            }
            $this->{$availableOption} = $overrideOptions->{$availableOption};
        }

        return $this;
    }

    /**
     * @param callable(ComponentReference $componentReference): ComponentInterface $buildComponentCallback
     * @param ?ComponentOptions $runtimeProcessConfiguration
     */
    public function replaceComponentReferences(
        $buildComponentCallback,
        ?ComponentOptions $runtimeProcessConfiguration = null
    ): void {
    }
}
