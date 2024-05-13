<?php

declare(strict_types=1);

namespace Novactive\eZPlatform\Bundles\Core;

class Component
{
    protected string $name;

    protected string $repo;

    /**
     * @param $name
     * @param $repo
     */
    public function __construct($name, $repo = null)
    {
        $this->name = $name;
        $this->repo = null !== $repo ? $repo : "Novactive/NovaeZ{$name}";
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRepo(): string
    {
        return $this->repo;
    }

    public function __toString()
    {
        return $this->name;
    }
}
