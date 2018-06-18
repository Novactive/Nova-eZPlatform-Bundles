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

use Doctrine\ORM\Mapping as ORM;
use Novactive\EzMenuManager\Traits\IdentityTrait;

/**
 * Class MenuItem.
 *
 * @ORM\Entity()
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({})
 * @ORM\Table(name="menu_manager_menu_item")
 *
 * @package Novactive\EzMenuManagerBundle\Entity
 */
abstract class MenuItem
{
    use IdentityTrait;

    /**
     * @ORM\Column(name="name", type="string", nullable=true)
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(name="url", type="string", nullable=true)
     *
     * @var string
     */
    protected $url;

    /**
     * @ORM\Column(name="target", type="string", nullable=true)
     *
     * @var string
     */
    protected $target;

    /**
     * @var Menu
     *
     * @ORM\ManyToOne(targetEntity="Novactive\EzMenuManagerBundle\Entity\Menu", inversedBy="items")
     */
    protected $menu;

    /**
     * @var MenuItem[]
     *
     * @ORM\OneToMany(
     *     targetEntity="Novactive\EzMenuManagerBundle\Entity\MenuItem",
     *     mappedBy="parent",
     *     cascade={"persist","remove"},
     *     fetch="EAGER",
     *     orphanRemoval=true
     *     )
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $childrens;

    /**
     * @var MenuItem
     *
     * @ORM\ManyToOne(targetEntity="Novactive\EzMenuManagerBundle\Entity\MenuItem", inversedBy="childrens")
     */
    protected $parent;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    protected $position;

    /**
     * @ORM\Column(name="options", type="json")
     *
     * @var array
     */
    protected $options = [];

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
     * @return string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getTarget(): ?string
    {
        return $this->target;
    }

    /**
     * @param string $target
     */
    public function setTarget(string $target): void
    {
        $this->target = $target;
    }

    /**
     * @return Menu
     */
    public function getMenu(): Menu
    {
        return $this->menu;
    }

    /**
     * @param MenuItem $menu
     */
    public function setMenu(MenuItem $menu): void
    {
        $this->menu = $menu;
    }

    /**
     * @return MenuItem[]
     */
    public function getChildrens(): array
    {
        return $this->childrens;
    }

    /**
     * @param MenuItem[] $childrens
     */
    public function setChildrens(array $childrens): void
    {
        $this->childrens = $childrens;
    }

    /**
     * @return MenuItem
     */
    public function getParent(): ?MenuItem
    {
        return $this->parent;
    }

    /**
     * @param MenuItem $parent
     */
    public function setParent(MenuItem $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getOption($name)
    {
        return $this->options[$name] ?? false;
    }

    /**
     * @param $name
     * @param $value
     */
    public function setOption($name, $value): void
    {
        $this->options[$name] = $value;
    }
}
