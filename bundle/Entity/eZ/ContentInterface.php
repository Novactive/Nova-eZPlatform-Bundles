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

use eZ\Publish\API\Repository\Values\Content\Content as eZContent;

/**
 * Interface ContentInterface.
 */
interface ContentInterface
{
    /**
     * @return int
     */
    public function getContentId(): int;

    /**
     * @param int $contentId
     *
     * @return ContentInterface
     */
    public function setContentId(int $contentId): ContentInterface;

    /**
     * @return eZContent
     */
    public function getContent(): eZContent;

    /**
     * @param eZContent $content
     *
     * @return ContentInterface
     */
    public function setContent(eZContent $content): ContentInterface;
}
