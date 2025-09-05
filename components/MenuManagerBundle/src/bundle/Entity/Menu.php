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

namespace Novactive\EzMenuManagerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Novactive\EzMenuManager\Traits\IdentityTrait;

/**
 * Class Menu.
 *
 * @ORM\Entity()
 * @ORM\Table(name="menu_manager_menu")
 *
 * @package Novactive\EzMenuManagerBundle\Entity
 */
class Menu
{
    use IdentityTrait;

    /**
     * @ORM\Column(name="name", type="string", nullable=false)
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(name="root_location_id", type="integer", nullable=true)
     *
     * @var int
     */
    protected $rootLocationId;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Novactive\EzMenuManagerBundle\Entity\MenuItem",
     *     mappedBy="menu",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     * @ORM\OrderBy({"position" = "ASC"})
     *
     * @var MenuItem[]|ArrayCollection
     */
    protected $items;

    /**
     * @ORM\Column(name="type", type="string", nullable=true)
     *
     * @var string|null
     */
    protected $type;

    /**
     * @ORM\Column(name="remote_id", type="string", nullable=true)
     *
     * @var string
     */
    protected $remoteId;

    /**
     * Menu constructor.
     */
    public function __construct(?string $remoteId = null)
    {
        $this->items = new ArrayCollection();
        $this->remoteId = $remoteId ?? md5(uniqid(get_class($this), true));
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getRootLocationId(): ?int
    {
        return $this->rootLocationId;
    }

    public function setRootLocationId(int $rootLocationId): void
    {
        $this->rootLocationId = $rootLocationId;
    }

    /**
     * @return MenuItem[]|ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param MenuItem $parent
     *
     * @return MenuItem[]|ArrayCollection
     */
    public function getItemsByParent($parent = null)
    {
        $criteria = new Criteria();
        $criteria->where(Criteria::expr()->eq('parent', $parent))
        ->orderBy(['position' => Criteria::ASC]);

        return $this->items->matching($criteria);
    }

    /**
     * @param MenuItem[] $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    public function addItem(MenuItem $menuItem)
    {
        if (false === $this->items->indexOf($menuItem)) {
            $menuItem->setMenu($this);
            $this->items->add($menuItem);
        }
    }

    public function removeItem(MenuItem $menuItem)
    {
        $this->items->removeElement($menuItem);
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getRemoteId(): ?string
    {
        return $this->remoteId;
    }

    public function setRemoteId(?string $remoteId): void
    {
        $this->remoteId = $remoteId;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->id;
    }

    public function assignPositions(): void
    {
        /** @var MenuItem[] $childrens */
        $childrens = $this->items->filter(function (MenuItem $item) {
            return null === $item->getParent();
        })->getValues();

        usort($childrens, function (MenuItem $itemA, MenuItem $itemB) {
            return $itemA->getPosition() <=> $itemB->getPosition();
        });

        $position = 0;
        foreach ($childrens as $item) {
            $item->setPosition($position);
            $item->assignPositions();
            ++$position;
        }
    }
}
