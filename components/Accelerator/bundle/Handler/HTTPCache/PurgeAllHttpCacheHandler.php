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

namespace Novactive\Bundle\eZAccelerator\Handler\HTTPCache;

use EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface;
use Novactive\Bundle\eZAccelerator\Message\HTTPCache\PurgeAllHttpCache;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PurgeAllHttpCacheHandler implements MessageHandlerInterface
{
    /**
     * @var PurgeClientInterface
     */
    private $cachePurger;

    public function __construct(PurgeClientInterface $cachePurger)
    {
        $this->cachePurger = $cachePurger;
    }

    public function __invoke(PurgeAllHttpCache $message): void
    {
        $this->cachePurger->purgeAll();
    }
}
