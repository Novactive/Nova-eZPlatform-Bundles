<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reference;

class Reference
{
    public const SCOPE_WORKFLOW = 10;
    public const SCOPE_ITEM = 20;

    protected string $name;
    protected int $scope;

    public function __construct(string $name, int $scope = self::SCOPE_ITEM)
    {
        $this->name = $name;
        $this->scope = $scope;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getScope(): int
    {
        return $this->scope;
    }
}
