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

namespace Novactive\EzMenuManager\FieldType\MenuItem\MenuItemStorage\Gateway;

use Doctrine\ORM\EntityManagerInterface;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;
use Novactive\EzMenuManager\FieldType\MenuItem\MenuItemStorage\Gateway;
use Novactive\EzMenuManager\FieldType\MenuItem\Value;
use Novactive\EzMenuManager\FieldType\MenuItem\ValueConverter;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;
use Novactive\EzMenuManagerBundle\Entity\MenuItem\ContentMenuItem;

class DoctrineStorage extends Gateway
{
    protected EntityManagerInterface $em;

    protected ValueConverter $valueConverter;

    /**
     * DoctrineStorage constructor.
     */
    public function __construct(EntityManagerInterface $em, ValueConverter $valueConverter)
    {
        $this->em = $em;
        $this->valueConverter = $valueConverter;
    }

    public function storeFieldData(VersionInfo $versionInfo, Field $field)
    {
    }

    public function getFieldData(VersionInfo $versionInfo, Field $field): void
    {
        $menuItems = $this->em->getRepository(MenuItem::class)->findBy(
            [
                'url' => ContentMenuItem::URL_PREFIX.$versionInfo->contentInfo->id,
            ]
        );

        $field->value->externalData = $this->valueConverter->toHash(new Value($menuItems));
    }

    public function deleteFieldData(VersionInfo $versionInfo, array $fieldIds): void
    {
        /** @var ContentMenuItem[] $menuItems */
        $menuItems = $this->em->getRepository(MenuItem::class)->findBy(
            [
                'url' => ContentMenuItem::URL_PREFIX.$versionInfo->contentInfo->id,
            ]
        );
        foreach ($menuItems as $menuItem) {
            if ($menuItem->hasChildrens()) {
                $menuItem->setName($versionInfo->names[$versionInfo->initialLanguageCode] ?? null);
                $this->em->persist($menuItem);
            } else {
                $this->em->remove($menuItem);
            }
        }
        $this->em->flush();
    }
}
