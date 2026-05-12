<?php

declare(strict_types=1);

namespace Novactive\StaticTemplates\Faker;

class Generator
{
    /**
     * @var array<GeneratorInterface>
     */
    private $generators = [];

    /**
     * @param iterable<GeneratorInterface> $generators
     */
    public function __construct(iterable $generators)
    {
        foreach ($generators as $generator) {
            $this->registerGenerator($generator);
        }
    }

    public function registerGenerator(GeneratorInterface $generator)
    {
        $this->generators[] = $generator;
    }

    public function generate(string $type)
    {
        foreach ($this->generators as $generator) {
            if ($generator->support($type)) {
                return $generator->generate($type);
            }
        }

        return $type;
    }
}
