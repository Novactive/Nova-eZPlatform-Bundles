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
     * @ORM\Column(name="url", type="text", nullable=true)
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
     * @ORM\ManyToOne(
     *     targetEntity="Novactive\EzMenuManagerBundle\Entity\MenuItem",
     *     inversedBy="childrens",
     *     )
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
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
     * @ORM\Column(name="remote_id", type="string", nullable=true)
     *
     * @var string
     */
    protected $remoteId;

    /**
     * MenuItem constructor.
     *
     * @param string|null $remoteId
     */
    public function __construct(?string $remoteId = null)
    {
        $this->childrens = new ArrayCollection();
        $this->options   = json_encode([]);
        $this->remoteId  = $remoteId ?? md5(uniqid(get_class($this), true));
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param $language
     *
     * @return string|null
     */
    public function getTranslatedName($language): ?string
    {
        $name = json_decode($this->getName(), true);

        return is_array($name) ? ($name[$language] ?? null) : $this->getName();
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
     * @param $language
     *
     * @return string|null
     */
    public function getTranslatedUrl($language): ?string
    {
        $url = json_decode($this->getUrl(), true);

        return is_array($url) ? ($url[$language] ?? null) : $this->getUrl();
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

    public function removeParent(): void
    {
        if ($this->parent) {
            $this->parent->removeChildren($this);
        }
        $this->parent = null;
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

    /**
     * @param array $properties
     */
    public function update(array $properties)
    {
        foreach ($properties as $property => $value) {
            $this->$property = $value;
        }
    }
}
