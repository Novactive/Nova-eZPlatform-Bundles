<?php

declare(strict_types=1);

namespace Novactive\StaticTemplates\Faker\Generators;

use Faker\Generator;
use InvalidArgumentException;
use Novactive\StaticTemplates\Faker\FakerGeneratorTrait;
use Novactive\StaticTemplates\Faker\GeneratorInterface;

class GenericGenerator implements GeneratorInterface
{
    use FakerGeneratorTrait;

    /**
     * @var Generator
     */
    protected $faker;

    public function __construct()
    {
        $this->faker = $this->getFaker();
    }

    public function support(string $type): bool
    {
        try {
            return null !== $this->faker->getFormatter($type);
        } catch (InvalidArgumentException $exception) {
            return false;
        }
    }

    public function generate(string $type)
    {
        return $this->faker->{$type};
    }
}
