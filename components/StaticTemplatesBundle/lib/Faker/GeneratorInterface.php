<?php

declare(strict_types=1);

namespace Novactive\StaticTemplates\Faker;

interface GeneratorInterface
{
    public function support(string $type): bool;

    public function generate(string $type);
}
