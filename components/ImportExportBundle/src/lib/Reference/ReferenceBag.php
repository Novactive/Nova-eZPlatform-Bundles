<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reference;

class ReferenceBag
{
    /** @var array{mixed, mixed} */
    protected array $references = [];

    public function addReference(string $name, $value, int $scope = Reference::SCOPE_ITEM): void
    {
        if (!isset($this->references[$scope])) {
            $this->references[$scope] = [];
        }
        $this->references[$scope][$name] = $value;
    }

    public function hasReference(string $name, int $scope = Reference::SCOPE_ITEM): bool
    {
        return isset($this->references[$scope][$name]);
    }

    public function getReference(string $name, $default = null, int $scope = Reference::SCOPE_ITEM)
    {
        return $this->references[$scope][$name] ?? $default;
    }

    public function resetScope(int $scope): void
    {
        unset($this->references[$scope]);
    }

    public function __set(string $name, $value): void
    {
        $this->addReference($name, $value);
    }

    public function __get(string $name)
    {
        return $this->getReference($name);
    }

    public function __isset(string $name): bool
    {
        return $this->hasReference($name);
    }
}
