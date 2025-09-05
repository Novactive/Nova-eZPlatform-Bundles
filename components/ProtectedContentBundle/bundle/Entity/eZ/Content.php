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

use Doctrine\ORM\Mapping as ORM;
use Ibexa\Contracts\Core\Repository\Values\Content\Content as eZContent;
use Ibexa\Contracts\Core\Repository\Values\Content\Location as eZLocation;
use Symfony\Component\Validator\Constraints as Assert;

trait Content
{
    /**
     * @var int
     *
     * @ORM\Column(name="content_id", type="integer", nullable=false)
     *
     * @Assert\NotBlank()
     */
    private $contentId;

    /**
     * @var eZContent
     */
    private $content;

    /**
     * @var eZLocation
     */
    private $location;

    public function getContent(): eZContent
    {
        return $this->content;
    }

    public function setContent(eZContent $content): ContentInterface
    {
        $this->content = $content;

        return $this;
    }

    public function getContentId(): int
    {
        return $this->contentId ?? 0;
    }

    public function setContentId(int $contentId): ContentInterface
    {
        $this->contentId = $contentId;

        return $this;
    }

    public function getLocation(): eZLocation
    {
        return $this->location;
    }

    public function setLocation(eZLocation $location): ContentInterface
    {
        $this->location = $location;

        return $this;
    }
}
