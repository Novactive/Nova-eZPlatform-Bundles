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

use Doctrine\ORM\Mapping as ORM;
use eZ\Publish\API\Repository\Values\Content\Content as eZContent;
use eZ\Publish\API\Repository\Values\Content\Location as eZLocation;

trait Content
{
    /**
     * @var int
     * @ORM\Column(name="EZ_locationId", type="integer", nullable=true)
     */
    private $locationId;

    /**
     * @var eZContent
     */
    private $content;

    /**
     * @var eZLocation
     */
    private $location;

    public function getContent(): ?eZContent
    {
        return $this->content;
    }

    public function setContent(eZContent $content): ContentInterface
    {
        $this->content = $content;

        return $this;
    }

    public function getLocationId(): ?int
    {
        return $this->locationId;
    }

    public function setLocationId(int $locationId): ContentInterface
    {
        $this->locationId = $locationId;

        return $this;
    }

    public function getLocation(): ?eZLocation
    {
        return $this->location;
    }

    public function setLocation(eZLocation $location): ContentInterface
    {
        $this->location = $location;

        return $this;
    }
}
