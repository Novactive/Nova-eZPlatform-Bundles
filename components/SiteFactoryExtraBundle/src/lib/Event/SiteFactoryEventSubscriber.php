<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaSiteFactoryExtra\Event;

use AlmaviaCX\Bundle\IbexaSiteFactoryExtra\Message\CreateMediaFolder;
use AlmaviaCX\Bundle\IbexaSiteFactoryExtra\Message\CreateUserGroup;
use AlmaviaCX\Bundle\IbexaSiteFactoryExtra\Message\UpdatePublicAccesses;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\SiteFactory\Events\CreateSiteEvent;
use Ibexa\Contracts\SiteFactory\Events\UpdateSiteEvent;
use Ibexa\SiteFactory\Event\CopySkeletonEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class SiteFactoryEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        protected MessageBusInterface $bus,
        protected PermissionResolver $permissionResolver,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            CreateSiteEvent::class => 'onCreateSite',
            UpdateSiteEvent::class => 'onUpdateSite',
            CopySkeletonEvent::class => 'onCopySkeleton',
        ];
    }

    public function onCreateSite(CreateSiteEvent $event)
    {
        $currentUserId = $this->permissionResolver->getCurrentUserReference()->getUserId();

        $this->bus->dispatch(
            new CreateUserGroup(
                $event->getSite()->id,
                $currentUserId
            )
        );
        $this->bus->dispatch(
            new CreateMediaFolder(
                $event->getSite()->id,
                $currentUserId
            )
        );
        $this->bus->dispatch(
            new UpdatePublicAccesses(
                $event->getSite()->id,
                true
            )
        );
    }

    public function onUpdateSite(UpdateSiteEvent $event)
    {
        $this->bus->dispatch(
            new UpdatePublicAccesses(
                $event->getSite()->id
            )
        );
    }

    public function onCopySkeleton(CopySkeletonEvent $event)
    {
    }
}
