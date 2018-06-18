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

namespace Novactive\EzMenuManagerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
     *     cascade={"persist", "remove"}
     *     )
     * @ORM\OrderBy({"weight" = "ASC"})
     *
     * @var MenuItem[]|ArrayCollection
     */
    protected $items;

    /**
     * Menu constructor.
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
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

    /**
     * @param int $rootLocationId
     */
    public function setRootLocationId(int $rootLocationId): void
    {
        $this->rootLocationId = $rootLocationId;
    }

    /**
     * @return MenuItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param MenuItem[] $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    /**
     * @param MenuItem $menuItem
     */
    public function addItem(MenuItem $menuItem)
    {
        $this->items->add($menuItem);
    }

    /**
     * @param MenuItem $menuItem
     */
    public function removeItem(MenuItem $menuItem)
    {
        $this->items->removeElement($menuItem);
    }
}
