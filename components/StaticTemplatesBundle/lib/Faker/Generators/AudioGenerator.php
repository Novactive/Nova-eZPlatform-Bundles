<?php

/**
 * @copyright Novactive
 * Date: 19/07/2022
 */

declare(strict_types=1);

namespace Novactive\StaticTemplates\Faker\Generators;

use Novactive\StaticTemplates\Faker\FakerGeneratorTrait;
use Novactive\StaticTemplates\Faker\Generator;
use Novactive\StaticTemplates\Faker\GeneratorInterface;
use Novactive\StaticTemplates\Values\Audio;
use Novactive\StaticTemplates\Values\AudioSource;

class AudioGenerator implements GeneratorInterface
{
    use FakerGeneratorTrait;

    protected Generator $generator;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    public function generateMatchRegex(string $parametersRegex = '(<([\S]+)>)?'): string
    {
        $fqcn = '\\'.Audio::class;

        return '^('.addslashes($fqcn).'|audio)(.*)?$';
    }

    public function support(string $type): bool
    {
        return 1 === preg_match('/'.$this->generateMatchRegex().'/', $type);
    }

    public function generate(string $type): Audio
    {
        $faker = $this->getFaker();

        $matches = [];
        preg_match('/'.$this->generateMatchRegex().'/', $type, $matches);
        $image = null;
        if ($matches[2]) {
            $image = $this->generator->generate('image'.$matches[2]);
        }

        $source = new AudioSource(
            [
                'name' => $faker->sentence(),
                'size' => $faker->numberBetween(0, 999999),
                'type' => 'audio/ogg',
                'uri' => 'https://cdn.plyr.io/static/demo/Kishi_Bashi_-_It_All_Began_With_a_Burst.ogg',
            ]
        );

        return new Audio([
                              'title' => $faker->sentence(),
                              'image' => $image,
                              'source' => $source,
                          ]);
    }
}
