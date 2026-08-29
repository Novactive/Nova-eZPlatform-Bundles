<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reference;

class Reference
{
    public const SCOPE_WORKFLOW = 10;
    public const SCOPE_ITEM = 20;

    public const CONFLICT_RESOLUTION_SKIP = 10;
    public const CONFLICT_RESOLUTION_OVERWRITE = 20;
    public const CONFLICT_RESOLUTION_APPEND = 30;

    public function __construct(
        protected string $name,
        protected int $scope = self::SCOPE_ITEM
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getScope(): int
    {
        return $this->scope;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
