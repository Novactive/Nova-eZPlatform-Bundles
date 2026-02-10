<?php

declare(strict_types=1);

namespace Novactive\eZPlatform\Bundles\Core;

class Component implements \Stringable
{
    protected string $repo;

    /**
     * @param $name
     * @param $repo
     */
    public function __construct(protected string $name, $repo = null)
    {
        $this->repo = $repo ?? "Novactive/NovaeZ{$this->name}";
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRepo(): string
    {
        return $this->repo;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
