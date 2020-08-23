<?php

/**
 * NovaeZMailingBundle Bundle.
 *
 * @package   Novactive\Bundle\eZMailingBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMailingBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZMailingBundle\Entity\eZ;

use eZ\Publish\API\Repository\Values\Content\Content as eZContent;
use eZ\Publish\API\Repository\Values\Content\Location as eZLocation;

interface ContentInterface
{
    public function getLocationId(): ?int;

    public function setLocationId(int $locationId): ContentInterface;

    public function getContent(): ?eZContent;

    public function setContent(eZContent $content): ContentInterface;

    public function getLocation(): ?eZLocation;

    public function setLocation(eZLocation $location): ContentInterface;
}
