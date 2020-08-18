<?php
/**
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 */

namespace Novactive\EzMenuManagerBundle\Entity\MenuItem;

use Doctrine\ORM\Mapping as ORM;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;

/**
 * Class ContentMenuItem.
 *
 * @ORM\Entity()
 *
 * @property int $contentId
 *
 * @package Novactive\EzMenuManagerBundle\Entity\MenuItem
 */
class ContentMenuItem extends MenuItem
{
    const URL_PREFIX = 'content:';

    /**
     * @return int
     */
    public function getContentId(): int
    {
        return (int) ltrim($this->getUrl(), static::URL_PREFIX);
    }

    /**
     * @param int $contentId
     */
    public function setContentId($contentId): void
    {
        if (!$contentId) {
            $this->setUrl(null);
        } else {
            $this->setUrl(static::URL_PREFIX.$contentId);
        }
    }
}
