<?php

namespace Novactive\NovaeZMaintenanceBundle\Listener;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Novactive\NovaeZMaintenanceBundle\Helper\FileHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class MaintenanceSubscriber implements EventSubscriberInterface
{
    /**
     * @var ConfigResolverInterface
     */
    private $configResolver;

    /**
     * @var Environment
     */
    protected $twig;

    /**
     * @var FileHelper
     */
    private $fileHelper;

    public function __construct(
        Environment $twig,
        ConfigResolverInterface $configResolver,
        FileHelper $fileHelper
    ) {
        $this->twig = $twig;
        $this->configResolver = $configResolver;
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
        if (true === $this->configResolver->getParameter('enable', 'nova_ezmaintenance')) {
            $filePath = $this->configResolver->getParameter('lock_file_id', 'nova_ezmaintenance');
            if ($this->fileHelper->existFileCluster($filePath)) {
                $event->setResponse($this->fileHelper->getResponse(503));
            }
        }
    }
}
