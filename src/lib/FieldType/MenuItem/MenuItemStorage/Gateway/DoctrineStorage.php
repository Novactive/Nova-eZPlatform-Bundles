<?php
/**
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 */

namespace Novactive\EzMenuManager\FieldType\MenuItem\MenuItemStorage\Gateway;

use Doctrine\ORM\EntityManagerInterface;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;
use Novactive\EzMenuManager\FieldType\MenuItem\MenuItemStorage\Gateway;
use Novactive\EzMenuManager\FieldType\MenuItem\Value;
use Novactive\EzMenuManager\FieldType\MenuItem\ValueConverter;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;
use Novactive\EzMenuManagerBundle\Entity\MenuItem\ContentMenuItem;

class DoctrineStorage extends Gateway
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var ValueConverter */
    protected $valueConverter;

    /**
     * DoctrineStorage constructor.
     *
     * @param EntityManagerInterface $em
     * @param ValueConverter         $valueConverter
     */
    public function __construct(EntityManagerInterface $em, ValueConverter $valueConverter)
    {
        $this->em             = $em;
        $this->valueConverter = $valueConverter;
    }

    public function storeFieldData(VersionInfo $versionInfo, Field $field)
    {
        /** @var ContentMenuItem[] $menuItems */
        $menuItems = $this->valueConverter->fromHash($field->value->data)->menuItems;
        if (!empty($menuItems)) {
            foreach ($menuItems as $menuItem) {
                $menuItem->setContentId($versionInfo->contentInfo->id);
                $this->em->persist($menuItem);
            }
        }

        $currentMenuItems = $this->em->getRepository(MenuItem::class)->findBy(
            [
                'url' => MenuItem\ContentMenuItem::URL_PREFIX.$versionInfo->contentInfo->id,
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

    public function getFieldData(VersionInfo $versionInfo, Field $field)
    {
        $menuItems = $this->em->getRepository(MenuItem::class)->findBy(
            [
                'url' => ContentMenuItem::URL_PREFIX.$versionInfo->contentInfo->id,
            ]
        );

        $field->value->data = $this->valueConverter->toHash(new Value($menuItems));
    }

    public function deleteFieldData(VersionInfo $versionInfo, array $fieldIds)
    {
        /** @var ContentMenuItem[] $menuItems */
        $menuItems = $this->em->getRepository(MenuItem::class)->findBy(
            [
                'url' => ContentMenuItem::URL_PREFIX.$versionInfo->contentInfo->id,
            ]
        );
        foreach ($menuItems as $menuItem) {
            if ($menuItem->hasChildrens()) {
                $newMenuItem = new MenuItem();
                $newMenuItem->setChildrens($menuItem->getChildrens());
                $newMenuItem->getPosition($menuItem->getPosition());

                $menuItem->getParent()->addChildren($newMenuItem);
                $menuItem->getMenu()->addItem($newMenuItem);
                $this->em->persist($newMenuItem);
            }
            $this->em->remove($menuItem);
        }

        $this->em->flush();
    }
}
