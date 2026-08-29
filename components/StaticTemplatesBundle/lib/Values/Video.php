<?php

/**
 * @copyright Novactive
 * Date: 18/07/2022
 */

declare(strict_types=1);

namespace Novactive\StaticTemplates\Values;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * @property VideoSource $defaultSource calls getDefaultSource()
 * @property ImageSource $posterSource  calls getPosterSource()
 * @property bool isExternalVideo calls isExternalVideo()
 * @property bool hasSource calls hasSource()
 */
class Video extends ValueObject implements MediaInterface
{
    public string $title;

    public string $duration;

    public ?string $legend;

    public string $credits;

    public string $transcript;

    public ?Image $image;

    /**
     * @var VideoSource[]
     */
    public array $sources = [];

    /**
     * @return string
     */
    public function getMediaType()
    {
        return 'video';
    }

    public function getDefaultSource(): ?VideoSource
    {
        return $this->hasSource() ? reset($this->sources) : null;
    }

    public function hasSource(): bool
    {
        return !empty($this->sources);
    }

    public function getPosterSource(): ?ImageSource
    {
        if ($this->image) {
            return $this->image->getDefaultSource();
        }

        return null;
    }

    public function isExternalVideo(): bool
    {
        return $this->getDefaultSource() instanceof ExternalVideoSource;
    }
}
