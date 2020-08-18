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
use eZ\Publish\Core\SignalSlot\Signal;
use eZ\Publish\Core\SignalSlot\Signal\ContentService\PublishVersionSignal;
use eZ\Publish\Core\SignalSlot\Slot;
use eZ\Publish\SPI\Persistence\Handler;
use Novactive\EzMenuManager\FieldType\MenuItem\ValueConverter;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;
use Novactive\EzMenuManagerBundle\Entity\MenuItem\ContentMenuItem;

class PublishVersionSignalSlot extends Slot
{
    /** @var Handler */
    protected $persistenceHandler;

    /** @var EntityManagerInterface */
    protected $em;

    /** @var ValueConverter */
    protected $valueConverter;

    /**
     * SignalListener constructor.
     *
     * @param Handler                $persistenceHandler
     * @param EntityManagerInterface $em
     * @param ValueConverter         $valueConverter
     */
    public function __construct(
        Handler $persistenceHandler,
        EntityManagerInterface $em,
        ValueConverter $valueConverter
    ) {
        $this->persistenceHandler = $persistenceHandler;
        $this->em                 = $em;
        $this->valueConverter     = $valueConverter;
    }

    /**
     * @param Signal $signal
     */
    public function receive(Signal $signal): void
    {
        if (!$signal instanceof PublishVersionSignal) {
            return;
        }

        $content = $this->persistenceHandler->contentHandler()->load($signal->contentId, $signal->versionNo);
        $fields  = $content->fields;
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
