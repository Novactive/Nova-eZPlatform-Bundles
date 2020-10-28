<?php

namespace Novactive\NovaeZMaintenanceBundle\Listener;

use Novactive\NovaeZMaintenanceBundle\Helper\FileHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class MaintenanceSubscriber implements EventSubscriberInterface
{
    /**
     * @var FileHelper
     */
    private $fileHelper;

    public function __construct(FileHelper $fileHelper)
    {
        $this->fileHelper = $fileHelper;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => [
                ['onKernelRequest', 10],
            ],
        ];
    }

    public function onKernelRequest(ResponseEvent $event): void
    {
        if ((true === $this->fileHelper->isMaintenanceEnabled()) && $this->fileHelper->isMaintenanceModeRunning()) {
            $event->setResponse($this->fileHelper->getResponse());
        }
    }
}
