<?php

namespace Novactive\NovaeZMaintenanceBundle\Listener;

use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use eZ\Publish\Core\MVC\Symfony\SiteAccess\SiteAccessAware;
use Novactive\NovaeZMaintenanceBundle\Helper\FileHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class MaintenanceSubscriber implements EventSubscriberInterface, SiteAccessAware
{
    /**
     * @var FileHelper
     */
    private $fileHelper;

    /**
     * @var SiteAccess|null
     */
    private $siteAccess;

    public function __construct(FileHelper $fileHelper)
    {
        $this->fileHelper = $fileHelper;
    }

    /**
     * @required
     */
    public function setSiteAccess(SiteAccess $siteAccess = null)
    {
        $this->siteAccess = $siteAccess;
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
        if (null !== $this->siteAccess) {
            $siteaccessName = $this->siteAccess->name;
            if (
                true === $this->fileHelper->isMaintenanceEnabled($siteaccessName)
                && true === $this->fileHelper->isMaintenanceModeRunning($siteaccessName)
            ) {
                $event->setResponse($this->fileHelper->getResponse($siteaccessName));
            }
        }
    }
}
