<?php

declare(strict_types=1);

namespace Novactive\StaticTemplates\Faker\Generators;

use Novactive\StaticTemplates\Faker\FakerGeneratorTrait;
use Novactive\StaticTemplates\Faker\GeneratorInterface;
use Novactive\StaticTemplates\Values\Coordinates;

class CoordinatesGenerator implements GeneratorInterface
{
    use FakerGeneratorTrait;

    public function support(string $type): bool
    {
        return 'coordinates' === $type;
    }

    public function generate(string $type)
    {
        $faker = $this->getFaker();
        $coordinates = [
            ['latitude' => 48.8566, 'longitude' => 2.3522],
            ['latitude' => 47.2184, 'longitude' => -1.5536],
            ['latitude' => 45.764, 'longitude' => 4.8357],
            ['latitude' => 44.8378, 'longitude' => -0.5792],
            ['latitude' => 43.6047, 'longitude' => 1.4442],
            ['latitude' => 48.0061, 'longitude' => -0.1996],
            ['latitude' => 47.6959, 'longitude' => 0.0746],
            ['latitude' => 47.69, 'longitude' => 0.08],
            ['latitude' => 49.1829, 'longitude' => 0.3707],
        ];

        return new Coordinates($faker->randomElement($coordinates));
    }
}
