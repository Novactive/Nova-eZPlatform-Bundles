<?php

declare(strict_types=1);

namespace Novactive\StaticTemplates\Faker\Generators;

use Novactive\StaticTemplates\Faker\Generator;
use Novactive\StaticTemplates\Faker\GeneratorInterface;

class DeterminedAssociativeArrayGenerator implements GeneratorInterface
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
        return 1 === preg_match('/^\{(.*)\}$/', $type);
    }

    public function generate(string $type): array
    {
        $matches = [];
        preg_match('/^\{(.*)\}$/', $type, $matches);

        $els = [];
        preg_match_all('/(\w+):([^,\s{\[]+|{\S+}(\[(\d+)?\])?|\[\S+\])/', $matches[1] ?? '', $els, PREG_SET_ORDER);

        $items = [];
        foreach ($els as $el) {
            [,$elKey, $elType] = $el;
            $items[trim($elKey)] = $this->generator->generate(trim($elType));
        }

        return $items;
    }
}
