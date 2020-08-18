<?php
/**
 * NovaeZMailingBundle Bundle.
 *
 * @package   Novactive\Bundle\eZMailingBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMailingBundle/blob/master/LICENSE MIT Licence
 */
declare(strict_types=1);

namespace Novactive\Bundle\eZMailingBundle\Listener;

use Psr\Log\LoggerInterface;
use Symfony\Component\Workflow\Event\Event;

/**
 * Class MailingWorkflow.
 */
class MailingWorkflow
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * MailingWorkflow constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Event $event
     */
    public function onWorkflowMailingLeave(Event $event): void
    {
        $this->logger->notice(
            sprintf(
                'Mailing %s (id: "%s") performed transaction "%s" from "%s" to "%s"',
                $event->getSubject()->getName(),
                $event->getSubject()->getId(),
                $event->getTransition()->getName(),
                implode(', ', array_keys($event->getMarking()->getPlaces())),
                implode(', ', $event->getTransition()->getTos())
            )
        );
    }
}
