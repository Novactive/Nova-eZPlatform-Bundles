<?php

declare(strict_types=1);

namespace Novactive\StaticTemplates\Values;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class Coordinates extends ValueObject
{
    public ?float $latitude;

    public ?float $longitude;
}
