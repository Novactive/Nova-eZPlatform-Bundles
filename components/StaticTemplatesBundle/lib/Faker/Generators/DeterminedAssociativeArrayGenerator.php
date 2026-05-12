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

        $entries = [];

        $strSplit = str_split($matches[1]);
        $entry = '';
        $isSubEl = 0;
        foreach ($strSplit as $char) {
            if (',' === $char && 0 === $isSubEl) {
                $entries[] = $entry;
                $entry = '';
                continue;
            }
            if (in_array($char, ['{', '['])) {
                ++$isSubEl;
            }
            if (in_array($char, ['}', ']'])) {
                --$isSubEl;
            }
            $entry .= $char;
        }
        $entries[] = $entry;

        $items = [];
        foreach ($entries as $entry) {
            preg_match('/(\w+):(.*)$/', $entry, $matches);

            [,$entryKey, $entryType] = $matches;
            $items[trim($entryKey)] = $this->generator->generate(trim($entryType));
        }

        return $items;
    }
}
