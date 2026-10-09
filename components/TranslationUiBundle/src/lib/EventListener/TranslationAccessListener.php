<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaTranslationUi\EventListener;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

final class TranslationAccessListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly PermissionResolver $permissionResolver,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $route = $event->getRequest()->attributes->get('_route');

        if (!is_string($route) || !str_starts_with($route, 'lexik_translation_')) {
            return;
        }

        if (!$this->permissionResolver->hasAccess('translation', 'manage')) {
            throw new AccessDeniedHttpException(
                'Access to translation management is restricted to administrators.'
            );
        }
    }
}
