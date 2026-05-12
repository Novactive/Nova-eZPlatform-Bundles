<?php

/**
 * @copyright Novactive
 * Date: 18/07/2022
 */

declare(strict_types=1);

namespace Novactive\StaticTemplates\Values;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * @property bool isExternalAudio calls isExternalAudio()
 * @property bool hasSource calls hasSource()
 */
class Audio extends ValueObject implements MediaInterface
{
    public string $title;

    public ?Image $image;

    public ?AudioSource $source;

    /**
     * @return string
     */
    public function getMediaType()
    {
        return 'audio';
    }

    public function hasSource(): bool
    {
        return !empty($this->source);
    }

    public function isExternalAudio(): bool
    {
        return $this->source instanceof ExternalAudioSource;
    }
}
