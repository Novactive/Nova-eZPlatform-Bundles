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

namespace Novactive\EzMenuManager\MenuItem\Type;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Novactive\EzMenuManager\MenuItem\AbstractMenuItemType;
use Novactive\EzMenuManager\MenuItem\MenuItemValue;
use Novactive\EzMenuManagerBundle\Entity\Menu;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;

class DefaultMenuItemType extends AbstractMenuItemType
{
    /** @var ConfigResolverInterface */
    protected $configResolver;

    /**
     * @required
     */
    public function setConfigResolver(ConfigResolverInterface $configResolver): void
    {
        $this->configResolver = $configResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityClassName(): string
    {
        return MenuItem::class;
    }

    /**
     * {@inheritdoc}
     */
    public function toHash(MenuItem $menuItem): array
    {
        $parent = $menuItem->getParent();

        return [
            'id' => $menuItem->getId(),
            'menuId' => $menuItem->getMenu()->getId(),
            'parentId' => $parent ? $parent->getId() : null,
            'position' => $menuItem->getPosition(),
            'url' => $menuItem->getUrl(),
            'name' => $menuItem->getName(),
            'target' => $menuItem->getTarget(),
            'options' => $menuItem->getOptions(),
            'type' => $this->getEntityClassName(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fromHash($hash): ?MenuItem
    {
        if (!is_array($hash)) {
            return null;
        }

        $menuItemRepo = $this->em->getRepository(MenuItem::class);
        $menuRepo = $this->em->getRepository(Menu::class);

        $menuItem = $this->getEntity(isset($hash['id']) && $hash['id'] ? $hash['id'] : null);
        $updateData = [
            'name' => $hash['name'] ?? false,
            'url' => $hash['url'] ?? false,
            'target' => $hash['target'] ?? false,
            'position' => $hash['position'] ?? 0,
        ];
        $menuItem->update(array_filter($updateData));

        $options = $hash['options'] ?? [];
        foreach ($options as $option => $value) {
            $menuItem->setOption($option, $value);
        }

        if (isset($hash['parentId']) && $hash['parentId']) {
            $parent = $menuItemRepo->find($hash['parentId']);
            $menuItem->setParent($parent);
        }

        if (isset($hash['menuId'])) {
            $menu = $menuRepo->find($hash['menuId']);
            if (!$menu) {
                return null;
            }
            $menuItem->setMenu($menu);
        }

        return $menuItem;
    }

    /**
     * @param $id
     *
     * @throws \ReflectionException
     *
     * @return MenuItem|object|null
     */
    protected function getEntity($id)
    {
        $menuItemRepo = $this->em->getRepository(MenuItem::class);

        $menuItem = $id ? $menuItemRepo->find($id) : null;
        if (!$menuItem) {
            $menuItem = $this->createEntity();
        }

        return $menuItem;
    }

    /**
     * {@inheritDoc}
     */
    public function toMenuItemLink(MenuItem $menuItem): ?MenuItemValue
    {
        $name = $this->getName($menuItem);
        if (null === $name) {
            return null;
        }

        $link = $this->createMenuItemValue($name);
        if (true === $menuItem->getOption('active', true)) {
            $url = $this->getUrl($menuItem);
            $link->setUri($url);
        }
        $link->setLinkAttribute('target', $menuItem->getTarget());

        return $link;
    }

    public function getName(MenuItem $menuItem): ?string
    {
        $languages = $this->getLanguages();
        foreach ($languages as $lang) {
            $value = $menuItem->getTranslatedName($lang);
            if (null !== $value) {
                return $value;
            }
        }

        return null;
    }

    public function getUrl(MenuItem $menuItem): ?string
    {
        $languages = $this->getLanguages();
        foreach ($languages as $lang) {
            $value = $menuItem->getTranslatedUrl($lang);
            if (null !== $value) {
                return $value;
            }
        }

        return null;
    }

    protected function getLanguages()
    {
        return $this->configResolver->getParameter('languages');
    }
}
