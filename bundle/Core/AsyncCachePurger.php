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

namespace Novactive\Bundle\eZAccelerator\Core;

use EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface;
use Novactive\Bundle\eZAccelerator\Message\HTTPCache\PurgeAllHttpCache;
use Novactive\Bundle\eZAccelerator\Message\HTTPCache\PurgeHttpCacheTags;

class AsyncCachePurger implements PurgeClientInterface
{
    /**
     * @var BusDispatcher
     */
    private $busDispatcher;

    public function __construct(BusDispatcher $busDispatcher)
    {
        $this->busDispatcher = $busDispatcher;
    }

    public function purge(array $tags): void
    {
        $this->busDispatcher->dispatch(new PurgeHttpCacheTags($tags));
    }

    public function purgeAll(): void
    {
        $this->busDispatcher->dispatch(new PurgeAllHttpCache());
    }
}
