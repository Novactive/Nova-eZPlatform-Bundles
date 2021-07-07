<?php

/**
 * NovaeZSlackBundle Bundle.
 *
 * @package   Novactive\Bundle\eZSlackBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZSlackBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZSlackBundle\Core\Event;

use Symfony\Contracts\EventDispatcher\Event;

class Searched extends Event
{
    private int $contentId;

    public function __construct(int $contentId)
    {
        $this->contentId = $contentId;
    }

    public function getContentId(): int
    {
        return $this->contentId;
    }
}
