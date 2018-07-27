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
 * Class MenuItem.
 *
 * @ORM\Entity()
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\Table(name="menu_manager_menu_item")
 *
 * @package Novactive\EzMenuManagerBundle\Entity
 */
class MenuItem
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
     * @var MenuItem[]|ArrayCollection
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
    protected $position = 0;

    /**
     * @ORM\Column(name="options", type="text")
     *
     * @var array
     */
    protected $options;

    /**
     * MenuItem constructor.
     */
    public function __construct()
    {
        $this->childrens = new ArrayCollection();
        $this->options   = json_encode([]);
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
     * @return string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url): void
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
     * @param Menu $menu
     */
    public function setMenu(Menu $menu): void
    {
        $this->menu = $menu;
    }

    /**
     * @return bool
     */
    public function hasChildrens(): bool
    {
        return !$this->childrens->isEmpty();
    }

    /**
     * @return MenuItem[]|ArrayCollection
     */
    public function getChildrens()
    {
        return $this->childrens;
    }

    /**
     * @param MenuItem[] $childrens
     */
    public function setChildrens($childrens): void
    {
        $this->childrens = $childrens;
    }

    /**
     * @param MenuItem $children
     *
     * @return array
     */
    public function addChildren(MenuItem $children): void
    {
        if (false === $this->childrens->indexOf($children)) {
            $children->setParent($this);
            $this->childrens->add($children);
        }
    }

    /**
     * @param MenuItem $children
     */
    public function removeChildren(MenuItem $children): void
    {
        $this->childrens->removeElement($children);
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
    public function setParent($parent): void
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
        return json_decode($this->options, true);
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->options = json_encode($options);
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getOption($name, $default = false)
    {
        $options = $this->getOptions();

        return $options[$name] ?? $default;
    }

    /**
     * @param $name
     * @param $value
     */
    public function setOption($name, $value): void
    {
        $options        = $this->getOptions();
        $options[$name] = $value;
        $this->setOptions($options);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->id;
    }
}
