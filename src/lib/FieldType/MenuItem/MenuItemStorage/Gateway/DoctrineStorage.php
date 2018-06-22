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
use Novactive\EzMenuManagerBundle\Entity\MenuItem;
use Novactive\EzMenuManagerBundle\Entity\MenuItem\ContentMenuItem;

class DoctrineStorage extends Gateway
{
    /** @var EntityManagerInterface */
    protected $em;

    /**
     * DoctrineStorage constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function storeFieldData(VersionInfo $versionInfo, Field $field)
    {
        /** @var ContentMenuItem[] $menuItems */
        $menuItems = $field->value->data;
        if (!empty($menuItems)) {
            foreach ($menuItems as $menuItem) {
                $menuItem->setContentId($versionInfo->contentInfo->id);
                $this->em->persist($menuItem);
            }

            $this->em->flush();
        }
    }

    public function getFieldData(VersionInfo $versionInfo, Field $field)
    {
        $menuItems = $this->em->getRepository(MenuItem::class)->findBy(
            [
                'url' => ContentMenuItem::URL_PREFIX.$versionInfo->contentInfo->id,
            ]
        );

        $field->value->data = $menuItems;
    }

    public function deleteFieldData(VersionInfo $versionInfo, array $fieldIds)
    {
        // TODO: Implement deleteFieldData() method.
    }
}
