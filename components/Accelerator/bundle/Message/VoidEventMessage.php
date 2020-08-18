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

namespace Novactive\Bundle\eZAccelerator\Message;

use Novactive\Bundle\eZAccelerator\Contracts\SiteAccessAware;
use Novactive\Bundle\eZAccelerator\Contracts\SiteAccessAwareInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class VoidEventMessage implements SiteAccessAwareInterface
{
    use SiteAccessAware;

    /**
     * @var array
     */
    private $payload;

    public function __construct(Event $event)
    {
        $this->payload = $this->convert($event);
    }

    private function convert(Event $event)
    {
        $payload = [];
        if (method_exists($event, 'getLocation')) {
            $payload['locationId'] = $event->getLocation()->id;
        }
        if (method_exists($event, 'getContent')) {
            $payload['contentId'] = $event->getContent()->id;
        }

        return (object) $payload;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }
}
