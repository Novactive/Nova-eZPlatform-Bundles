<?php

declare(strict_types=1);

namespace Novactive\StaticTemplates\Faker\Generators;

use Novactive\StaticTemplates\Faker\Generator;
use Novactive\StaticTemplates\Faker\GeneratorInterface;

class ArrayGenerator implements GeneratorInterface
{
    /**
     * @var \Novactive\StaticTemplates\Faker\Generator
     */
    private $generator;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    public function support(string $type): bool
    {
        return 1 === preg_match('/\[(\d+)?\]$/', $type);
    }

    public function generate(string $type): array
    {
        preg_match('/(\S+)\[(\d+)?\]$/', $type, $matches);
        $count = $matches[2] ?? rand(1, 10);
        $items = [];
        for ($i = 0; $i < $count; ++$i) {
            $items[] = $this->generator->generate($matches[1]);
        }

        return $items;
    }
}
