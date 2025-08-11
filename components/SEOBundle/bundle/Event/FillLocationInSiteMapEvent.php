<?php

namespace Novactive\Bundle\eZSEOBundle\Event;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Événement déclenché avant d'ajouter $location au sitemap.xml.
 *
 * Permet d'invalider la Location si elle ne doit pas être dans le sitemap.
 */
class FillLocationInSiteMapEvent extends Event
{
    protected bool $isValid = true;

    public function __construct(protected readonly Location $location)
    {
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function setIsValid(bool $isValide): void
    {
        $this->isValid = $isValide;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }
}
