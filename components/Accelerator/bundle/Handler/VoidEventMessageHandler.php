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

namespace Novactive\Bundle\eZAccelerator\Handler;

use Novactive\Bundle\eZAccelerator\Message\VoidEventMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class VoidEventMessageHandler implements MessageHandlerInterface
{
    public function __invoke(VoidEventMessage $message): void
    {
        // just a default that does nothing
    }
}
