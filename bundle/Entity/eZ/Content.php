<?php
/**
 * NovaeZMailingBundle Bundle.
 *
 * @package   Novactive\Bundle\eZMailingBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/eZMailingBundle/blob/master/LICENSE MIT Licence
 */
declare(strict_types=1);

namespace Novactive\Bundle\eZMailingBundle\Entity\eZ;

use Doctrine\ORM\Mapping as ORM;
use eZ\Publish\API\Repository\Values\Content\Content as eZContent;

/**
 * Trait Content.
 */
trait Content
{
    /**
     * @var int
     * @ORM\Column(name="EZ_contentId", type="integer", nullable=true)
     */
    private $contentId;

    /**
     * @var eZContent
     */
    private $content;

    /**
     * {@inheritdoc}
     */
    public function getContentId(): int
    {
        return $this->contentId;
    }

    /**
     * {@inheritdoc}
     */
    public function setContentId(int $contentId): ContentInterface
    {
        $this->contentId = $contentId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent(): eZContent
    {
        return $this->content;
    }

    /**
     * {@inheritdoc}
     */
    public function setContent(eZContent $content): ContentInterface
    {
        $this->content = $content;

        return $this;
    }
}
