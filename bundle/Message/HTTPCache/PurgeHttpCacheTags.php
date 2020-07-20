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

namespace Novactive\Bundle\eZAccelerator\Message\HTTPCache;

use Novactive\Bundle\eZAccelerator\Contracts\SiteAccessAware;
use Novactive\Bundle\eZAccelerator\Contracts\SiteAccessAwareInterface;

final class PurgeHttpCacheTags implements SiteAccessAwareInterface
{
    use SiteAccessAware;

    /**
     * @var array
     */
    private $tags;

    public function __construct(array $tags)
    {
        $this->tags = $tags;
    }

    public function getTags(): array
    {
        return $this->tags;
    }
}
