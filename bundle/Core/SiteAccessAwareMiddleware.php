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

use Novactive\Bundle\eZAccelerator\Contracts\SiteAccessAware as SiteAccessAwareTrait;
use Novactive\Bundle\eZAccelerator\Contracts\SiteAccessAwareInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class SiteAccessAwareMiddleware implements MiddlewareInterface, SiteAccessAwareInterface
{
    use SiteAccessAwareTrait;

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();
        if ($message instanceof SiteAccessAwareInterface && null !== $this->siteAccess &&
            null === $message->getSiteAccess()) {
            $message->setSiteAccess($this->siteAccess);
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
