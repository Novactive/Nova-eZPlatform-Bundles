<?php

/**
 * @copyright Novactive
 * Date: 18/07/2022
 */

declare(strict_types=1);

namespace Novactive\StaticTemplates\Values;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class AudioSource extends ValueObject
{
    public string $name;

    public int $size;

    public string $type;

    public string $uri;
}
