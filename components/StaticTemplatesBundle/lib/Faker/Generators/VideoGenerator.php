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
use Novactive\StaticTemplates\Values\Video;
use Novactive\StaticTemplates\Values\VideoSource;

class VideoGenerator implements GeneratorInterface
{
    use FakerGeneratorTrait;

    protected Generator $generator;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    public function generateMatchRegex(string $parametersRegex = '(<([\S]+)>)?'): string
    {
        $fqcn = '\\'.Video::class;

        return '^('.addslashes($fqcn).'|video)(.*)?$';
    }

    public function support(string $type): bool
    {
        return 1 === preg_match('/'.$this->generateMatchRegex().'/', $type);
    }

    public function generate(string $type): Video
    {
        $faker = $this->getFaker();

        $matches = [];
        preg_match('/'.$this->generateMatchRegex().'/', $type, $matches);
        $image = null;
        if ($matches[2]) {
            $image = $this->generator->generate('image'.$matches[2]);
        }

        $source = new VideoSource(
            [
                'name' => $faker->sentence(),
                'type' => 'video/mp4',
                'uri' => 'https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-576p.mp4',
            ]
        );

        return new Video([
                              'title' => $faker->sentence(),
                              'duration' => $faker->randomNumber(2, true),
                              'credits' => $faker->sentence(),
                              'legend' => $faker->sentence(),
                              'transcript' => $this->generator->generate('richtext'),
                              'image' => $image,
                              'sources' => [$source],
                          ]);
    }
}
