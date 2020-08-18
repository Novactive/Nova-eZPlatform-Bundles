<?php

/**
 * Nova eZ Accelerator.
 *
 * @package   Novactive\Bundle\eZAccelerator
 *
 * @author    Novactive <dir.tech@novactive.com>
 * @author    Sébastien Morel (Plopix) <morel.seb@gmail.com>
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZAccelerator/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZAccelerator\Message\Search;

use Novactive\Bundle\eZAccelerator\Contracts\SiteAccessAware;
use Novactive\Bundle\eZAccelerator\Contracts\SiteAccessAwareInterface;

final class UnindexLocation implements SiteAccessAwareInterface
{
    use SiteAccessAware;

    /**
     * @var int
     */
    private $contentId;

    /**
     * @var int
     */
    private $locationId;

    public function __construct(int $contentId, int $locationId)
    {
        $this->contentId = $contentId;
        $this->locationId = $locationId;
    }

    public function getContentId(): int
    {
        return $this->contentId;
    }

    public function getLocationId(): int
    {
        return $this->locationId;
    }
}
