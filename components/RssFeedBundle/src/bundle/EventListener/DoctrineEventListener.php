<?php

declare(strict_types=1);

namespace Novactive\EzRssFeedBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Ibexa\Contracts\HttpCache\PurgeClient\PurgeClientInterface;
use Novactive\EzRssFeedBundle\Entity\RssFeeds;

class DoctrineEventListener
{
    /** @var PurgeClientInterface */
    protected $httpCachePurgeClient;

    public function __construct(PurgeClientInterface $httpCachePurgeClient)
    {
        $this->httpCachePurgeClient = $httpCachePurgeClient;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->lifecycleEventHandler($args);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->lifecycleEventHandler($args);
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $this->lifecycleEventHandler($args);
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    protected function lifecycleEventHandler(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if ($entity instanceof RssFeeds) {
            $tags = ['rssfeed-'.$entity->getId(), 'rssfeeds'];
            $this->invalidateTags($tags);
        }
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    protected function invalidateTags(array $tags): void
    {
        if (!empty($tags)) {
            $this->httpCachePurgeClient->purge($tags);
        }
    }
}
