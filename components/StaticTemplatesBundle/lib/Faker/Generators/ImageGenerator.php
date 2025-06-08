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

    protected array $breakpoints = [];
    protected ?string $providerClass = null;
    protected array $providerParameters = [];

    public function __construct(array $breakpoints, array $providerParameters = [], ?string $providerClass = null)
    {
        $this->breakpoints = $breakpoints;
        $this->providerParameters = $providerParameters;
        $this->providerClass = $providerClass;
    }

    public function generateMatchRegex(string $parametersRegex = '(<([\S]+)>)?'): string
    {
        $fqcn = '\\'.Image::class;

        $regex = '^('.addslashes($fqcn).'|image)';

        $breakpointCount = count($this->breakpoints);
        for ($i = 0; $i < $breakpointCount; ++$i) {
            $regex .= '(<(\d+)?x(\d+)?>)?';
        }

        return $regex.'$';
    }

    public function support(string $type): bool
    {
        return 1 === preg_match('/'.$this->generateMatchRegex().'/', $type);
    }

    public function generate(string $type): Image
    {
        $faker = $this->getFaker();
        if ($this->providerClass) {
            $faker->addProvider(new $this->providerClass($faker));
        }

        $matches = [];
        preg_match('/'.$this->generateMatchRegex().'/', $type, $matches);

        $sourceRequirements = [];
        foreach ($this->breakpoints as $i => $breakpoint) {
            $index = 2 + $i * 3;
            if (isset($matches[$index])) {
                $sourceRequirements[] = [
                    'width' => $matches[$index + 1] ?? null,
                    'height' => $matches[$index + 2] ?? null,
                    'media' => $breakpoint,
                ];
            }
        }

        $sources = [];
        foreach ($sourceRequirements as $sourceReqs) {
            $width = !empty($sourceReqs['width']) ? $sourceReqs['width'] : $faker->numberBetween(100, 1000);
            $height = !empty($sourceReqs['height']) ? $sourceReqs['height'] : $faker->numberBetween(3, 1000);

            $uris = [
                call_user_func_array(
                    [$faker, 'imageUrl'],
                    ['width' => $width, 'height' => $height, ...$this->providerParameters]
                ),
                call_user_func_array(
                    [$faker, 'imageUrl'],
                    ['width' => $width * 2, 'height' => $height * 2, ...$this->providerParameters]
                ).' 2x',
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
