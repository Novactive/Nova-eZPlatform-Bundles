<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Component;

use Exception;
use ReflectionClass;

class ComponentOptions
{
    /**
     * @var array<string, bool>
     */
    protected array $initializationState = [];

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(array $options = [])
    {
        $availableOptions = $this->getAvailableOptions();
        foreach ($availableOptions as $option) {
            $this->initializationState[$option] = false;
        }

        foreach ($options as $option => $value) {
            $this->__set($option, $value);
        }
    }

    /**
     * @return array<string, bool>
     */
    public function getInitializationState(): array
    {
        return $this->initializationState;
    }

    /**
     * @return string[]
     */
    public function getInitializedOptions(): array
    {
        return array_keys(array_filter($this->initializationState, static function ($option) {
            return true === $option;
        }));
    }

    /**
     * @return string[]
     */
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

    /**
     * @return iterable<string>
     */
    public function getAvailableOptions(): iterable
    {
        $properties = (new ReflectionClass(static::class))->getProperties();
        foreach ($properties as $property) {
            if ('initializationState' === $property->getName()) {
                continue;
            }
            yield $property->getName();
        }
    }

    /**
     * @throws Exception
     */
    public function __set(string $name, mixed $value)
    {
        if (property_exists($this, $name)) {
            $this->initializationState[$name] = true;
            $this->{$name} = $value;

            return;
        }
        $className = static::class;
        throw new Exception("Option '{$name}' not found on '{$className}'");
    }

    /**
     * @return null
     */
    public function __get(string $name)
    {
        if (!property_exists($this, $name)) {
            return null;
        }

        return $this->{$name};
    }

    /**
     * @param static $overrideOptions
     *
     * @return $this
     */
    public function merge($overrideOptions): static
    {
        $availableOptions = $this->getAvailableOptions();
        foreach ($availableOptions as $availableOption) {
            if (!isset($overrideOptions->{$availableOption})) {
                continue;
            }
            $this->{$availableOption} = $overrideOptions->{$availableOption};
        }

        return $this;
    }

    /**
     * @param callable(ComponentReference $componentReference, ?ComponentOptions $runtimeProcessConfiguration): ComponentInterface<ComponentOptions> $componentBuilder
     * @param static|null $runtimeProcessConfiguration
     */
    public function replaceComponentReferences(
        callable $componentBuilder,
        ?ComponentOptions $runtimeProcessConfiguration = null
    ): void {
    }
}
