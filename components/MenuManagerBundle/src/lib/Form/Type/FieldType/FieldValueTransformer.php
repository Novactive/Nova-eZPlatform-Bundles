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

namespace Novactive\EzMenuManager\Form\Type\FieldType;

use Doctrine\ORM\EntityManagerInterface;
use Novactive\EzMenuManager\FieldType\MenuItem\Value;
use Novactive\EzMenuManagerBundle\Entity\Menu;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;
use Novactive\EzMenuManagerBundle\Entity\MenuItem\ContentMenuItem;
use Symfony\Component\Form\DataTransformerInterface;

class FieldValueTransformer implements DataTransformerInterface
{
    /** @var EntityManagerInterface */
    protected $em;

    /**
     * FieldValueTransformer constructor.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms a FieldType Value into a hash using `FieldTpe::toHash()`.
     * This hash is compatible with `reverseTransform()`.
     *
     * @return array|null the value's hash, or null if $value was not a FieldType Value
     */
    public function transform($value)
    {
        if (!$value instanceof Value) {
            return null;
        }

        $hash = [];
        foreach ($value->menuItems as $menuItem) {
            $parent = $menuItem->getParent();
            $hash[] = [
                'id' => $menuItem->getId(),
                'menuId' => $menuItem->getMenu()->getId(),
                'parentId' => $parent ? $parent->getId() : null,
                'position' => $menuItem->getPosition(),
            ];
        }

        return json_encode($hash);
    }

    /**
     * Transforms a hash into a FieldType Value using `FieldType::fromHash()`.
     * The FieldValue is compatible with `transform()`.
     *
     * @return \eZ\Publish\SPI\FieldType\Value
     */
    public function reverseTransform($value)
    {
        $menuRepo = $this->em->getRepository(Menu::class);
        $menuItemRepo = $this->em->getRepository(MenuItem::class);
        $hash = json_decode($value, true);
        $menuItems = [];
        foreach ($hash as $hashItem) {
            if ($hashItem['id']) {
                $menuItem = $menuItemRepo->find($hashItem['id']);
            } else {
                $menuItem = new ContentMenuItem();
            }
            $parent = $menuItemRepo->find($hashItem['parentId']);
            if ($hashItem['parentId'] && $parent) {
                $parent->addChildren($menuItem);
            }
            $menuItem->setMenu($menuRepo->find($hashItem['menuId']));
            $menuItem->setPosition($hashItem['position'] ?? 0);
            $menuItems[] = $menuItem;
        }

        return new Value($menuItems);
    }
}
