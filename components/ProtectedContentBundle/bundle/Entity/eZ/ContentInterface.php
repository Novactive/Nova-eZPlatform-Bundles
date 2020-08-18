<?php

/**
 * NovaeZProtectedContentBundle.
 *
 * @package   Novactive\Bundle\eZProtectedContentBundle
 *
 * @author    Novactive
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/eZProtectedContentBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZProtectedContentBundle\Entity\eZ;

use eZ\Publish\API\Repository\Values\Content\Content as eZContent;
use eZ\Publish\API\Repository\Values\Content\Location as eZLocation;

interface ContentInterface
{
    public function getContentId(): int;

    public function setContentId(int $contentId): ContentInterface;

    public function getContent(): eZContent;

    public function setContent(eZContent $content): ContentInterface;

    public function getLocation(): eZLocation;

    public function setLocation(eZLocation $location): ContentInterface;
}
