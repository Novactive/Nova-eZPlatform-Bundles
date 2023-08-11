<?php

/**
 * @copyright Novactive
 * Date: 19/07/2022
 */

declare(strict_types=1);

namespace Novactive\StaticTemplates\Faker\Generators;

use Novactive\StaticTemplates\Faker\FakerGeneratorTrait;
use Novactive\StaticTemplates\Faker\GeneratorInterface;
use Novactive\StaticTemplates\Values\Image;
use Novactive\StaticTemplates\Values\ImageSource;

class ImageGenerator implements GeneratorInterface
{
    use FakerGeneratorTrait;

    public function generateMatchRegex(string $parametersRegex = '(<([\S]+)>)?'): string
    {
        $fqcn = '\\'.Image::class;

        return '^('.addslashes($fqcn).'|image)(<(\d+)?x(\d+)?>)?(<(\d+)?x(\d+)?>)?(<(\d+)?x(\d+)?>)?$';
    }

    public function support(string $type): bool
    {
        return 1 === preg_match('/'.$this->generateMatchRegex().'/', $type);
    }

    public function generate(string $type): Image
    {
        $faker = $this->getFaker();

        $matches = [];
        preg_match('/'.$this->generateMatchRegex().'/', $type, $matches);
        $sourceRequirements = [
            [
                'width' => $matches[3] ?? null,
                'height' => $matches[4] ?? null,
                'media' => '(min-width: 1024px)',
            ],
        ];

        if (isset($matches[5])) {
            $sourceRequirements[] = [
                'width' => $matches[6] ?? null,
                'height' => $matches[7] ?? null,
                'media' => '(min-width: 754px)',
            ];
        }
        if (isset($matches[8])) {
            $sourceRequirements[] = [
                'width' => $matches[9] ?? null,
                'height' => $matches[10] ?? null,
                'media' => '(min-width: 0)',
            ];
        }

        $sources = [];
        foreach ($sourceRequirements as $sourceReqs) {
            $width = !empty($sourceReqs['width']) ? $sourceReqs['width'] : $faker->numberBetween(100, 1000);
            $height = !empty($sourceReqs['height']) ? $sourceReqs['height'] : $faker->numberBetween(3, 1000);
            $uris = [
                $faker->imageUrl($width, $height, null, false, null, true),
                $faker->imageUrl($width * 2, $height * 2, null, false, null, true).' 2x',
            ];
            $sources[] = new ImageSource([
               'uri' => implode(', ', $uris),
               'width' => $width,
               'height' => $height,
               'media' => $sourceReqs['media'],
            ]);
        }

        return new Image([
             'alt' => $faker->sentence(),
             'caption' => $faker->sentence(),
             'credit' => $faker->sentence(),
             'sources' => $sources,
         ]);
    }
}
