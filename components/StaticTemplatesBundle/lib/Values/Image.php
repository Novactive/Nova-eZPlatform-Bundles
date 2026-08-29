<?php

/**
 * @copyright Novactive
 * Date: 18/07/2022
 */

declare(strict_types=1);

namespace Novactive\StaticTemplates\Values;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * @property ImageSource $defaultSource calls getDefaultSource()
 * @property bool hasSource calls hasSource()
 */
class Image extends ValueObject implements MediaInterface
{
    public ?string $alt;

    public ?string $caption;

    public ?string $credit;

    /**
     * @var ImageSource[]
     */
    public array $sources = [];

    /**
     * @return string
     */
    public function getMediaType()
    {
        return 'image';
    }

    /**
     * @return ImageSource
     */
    public function getDefaultSource(): ?ImageSource
    {
        return $this->hasSource() ? reset($this->sources) : null;
    }

    public function hasSource(): bool
    {
        return !empty($this->sources);
    }
}
