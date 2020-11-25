<?php

/**
 * Nova eZ Algolia Search Engine.
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @licence   "SEE FULL LICENSE OPTIONS IN LICENSE.md"
 *            Nova eZ Algolia Search Engine is tri-licensed, meaning you must choose one of three licenses to use:
 *                - Commercial License: a paid license, meant for commercial use. The default option for most users.
 *                - Creative Commons Non-Commercial No-Derivatives: meant for trial and non-commercial use.
 *                - GPLv3 License: meant for open-source projects.
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZAlgoliaSearchEngine\Event;

use eZ\Publish\SPI\Persistence\Content\Location;
use Novactive\Bundle\eZAlgoliaSearchEngine\Mapping\Document;

final class LocationIndexCreateEvent extends DocumentCreateEvent
{
    /**
     * @var Location
     */
    private $location;

    public function __construct(Location $location, Document $document)
    {
        parent::__construct($document);
        $this->location = $location;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }
}
