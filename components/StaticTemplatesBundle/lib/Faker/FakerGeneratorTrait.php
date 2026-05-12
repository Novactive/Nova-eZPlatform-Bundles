<?php

declare(strict_types=1);

namespace Novactive\StaticTemplates\Faker;

use Faker\Factory;
use Faker\Generator;

trait FakerGeneratorTrait
{
    public function getFaker(): Generator
    {
        return Factory::create();
    }
}
