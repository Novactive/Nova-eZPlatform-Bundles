<?php

namespace Novactive\NovaeZMaintenanceBundle\Listener;

use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessAware;
use Novactive\NovaeZMaintenanceBundle\Helper\FileHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
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
    public function setSiteAccess(SiteAccess $siteAccess = null): void
    {
        $this->siteAccess = $siteAccess;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['onKernelRequest', 10],
            ],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (null !== $this->siteAccess) {
            $siteaccessName = $this->siteAccess->name;
            if (
                true === $this->fileHelper->isMaintenanceEnabled($siteaccessName)
                && true === $this->fileHelper->isMaintenanceModeRunning($siteaccessName)
                && false === $this->fileHelper->isClientIpAuthorized(
                    $event->getRequest()->getClientIp(),
                    $this->siteAccess
                )
            ) {

                // In case front siteaccess locked and back not locked
                // Include request used in preview mode page builder
                $isPreviewPageBuilder = false;
                if(
                    $event->getRequest()->attributes->get('_route') != 'ibexa.url.alias' &&
                    strstr($this->siteAccess->name, 'admin') === false
                ){
                    $isPreviewPageBuilder = true;
                }

                if(!$isPreviewPageBuilder){
                    $event->setResponse($this->fileHelper->getResponse($siteaccessName));
                }
            }
        }
    }
}
