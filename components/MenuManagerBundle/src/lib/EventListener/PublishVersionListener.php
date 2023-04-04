<?php

/**
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 */

namespace Novactive\EzMenuManager\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Ibexa\Contracts\Core\Persistence\Handler as PersistenceHandler;
use Ibexa\Contracts\Core\Repository\Events\Content\PublishVersionEvent;
use Novactive\EzMenuManager\FieldType\MenuItem\ValueConverter;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;
use Novactive\EzMenuManagerBundle\Entity\MenuItem\ContentMenuItem;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PublishVersionListener implements EventSubscriberInterface
{
    /** @var PersistenceHandler */
    protected $persistenceHandler;

    /** @var EntityManagerInterface */
    protected $em;

    /** @var ValueConverter */
    protected $valueConverter;

    /**
     * SignalListener constructor.
     */
    public function __construct(
        PersistenceHandler $persistenceHandler,
        EntityManagerInterface $em,
        ValueConverter $valueConverter
    ) {
        $this->persistenceHandler = $persistenceHandler;
        $this->em = $em;
        $this->valueConverter = $valueConverter;
    }

    public static function getSubscribedEvents()
    {
        return [
            PublishVersionEvent::class => 'onPublishVersion',
        ];
    }

    public function onPublishVersion(PublishVersionEvent $event): void
    {
        $content = $this->persistenceHandler->contentHandler()->load(
            $event->getContent()->id,
            $event->getVersionInfo()->versionNo
        );
        $fields = $content->fields;
        foreach ($fields as $field) {
            if ('menuitem' !== $field->type) {
                continue;
            }

            /** @var ContentMenuItem[] $menuItems */
            $menuItems = $this->valueConverter->fromHash($field->value->data)->menuItems;
            if (!empty($menuItems)) {
                foreach ($menuItems as $menuItem) {
                    $menuItem->setContentId($content->versionInfo->contentInfo->id);
                    $this->em->persist($menuItem);
                }
            }

            $currentMenuItems = $this->em->getRepository(MenuItem::class)->findBy(
                [
                    'url' => MenuItem\ContentMenuItem::URL_PREFIX.$content->versionInfo->contentInfo->id,
                ]
            );
            if (!empty($currentMenuItems)) {
                $menuItemsToDelete = array_udiff(
                    $currentMenuItems,
                    $menuItems,
                    function (MenuItem $currentMenuItem, MenuItem $menuItem) {
                        return $currentMenuItem->getId() - $menuItem->getId();
                    }
                );

                if (!empty($menuItemsToDelete)) {
                    foreach ($menuItemsToDelete as $menuItemToDelete) {
                        $this->em->remove($menuItemToDelete);
                    }
                }
            }

            $this->em->flush();
        }
    }
}
