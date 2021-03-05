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

namespace Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider\Action;

use Novactive\Bundle\eZSlackBundle\Core\Slack\Action;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Attachment;
use Novactive\Bundle\eZSlackBundle\Core\Slack\InteractiveMessage;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Interface ActionProviderInterface.
 */
interface ActionProviderInterface
{
    public function setAlias(string $alias): void;

    public function getAlias(): string;

    public function getAction(Event $event, int $index): ?Action;

    /**
     * @param $alias
     */
    public function supports($alias): bool;

    public function execute(InteractiveMessage $message, array $actions = []): array;
}
