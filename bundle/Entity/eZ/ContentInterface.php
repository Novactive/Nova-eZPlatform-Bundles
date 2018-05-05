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

/**
 * Interface ContentInterface.
 */
interface ContentInterface
{
    /**
     * @return int
     */
    public function getLocationId(): ?int;

    /**
     * @param int $locationId
     *
     * @return ContentInterface
     */
    public function setLocationId(int $locationId): ContentInterface;

    /**
     * @return eZContent
     */
    public function getContent(): ?eZContent;

    /**
     * @param eZContent $content
     *
     * @return ContentInterface
     */
    public function setContent(eZContent $content): ContentInterface;

    /**
     * @return eZLocation
     */
    public function getLocation(): ?eZLocation;

    /**
     * @param eZLocation $location
     */
    public function setLocation(eZLocation $location): ContentInterface;
}
