<?php

declare(strict_types=1);

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
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Novactive\EzMenuManager\Traits\IdentityTrait;

#[ORM\Entity]
#[ORM\Table(name: 'menu_manager_menu_item')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
class MenuItem implements \Stringable
{
    use IdentityTrait;

    #[ORM\Column(name: 'name', type: 'string', nullable: true)]
    protected ?string $name = null;

    #[ORM\Column(name: 'url', type: 'text', nullable: true)]
    protected ?string $url = null;

    #[ORM\Column(name: 'target', type: 'string', nullable: true)]
    protected ?string $target = null;

    #[ORM\ManyToOne(targetEntity: Menu::class, inversedBy: 'items')]
    protected Menu $menu;

    #[ORM\OneToMany(
        targetEntity: MenuItem::class,
        mappedBy: 'parent',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    #[ORM\OrderBy(['position' => 'ASC'])]
    protected Collection $childrens;

    #[ORM\ManyToOne(targetEntity: MenuItem::class, inversedBy: 'childrens')]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id')]
    protected ?MenuItem $parent = null;

    #[ORM\Column(name: 'position', type: 'integer')]
    protected int $position = 0;

    #[ORM\Column(name: 'options', type: 'text')]
    protected string $options;

    #[ORM\Column(name: 'remote_id', type: 'string', nullable: true)]
    protected string $remoteId;

    /**
     * MenuItem constructor.
     */
    public function __construct(?string $remoteId = null)
    {
        $this->childrens = new ArrayCollection();
        $this->options = json_encode([]);
        $this->remoteId = $remoteId ?? md5(uniqid(static::class, true));
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getRemoteId(): ?string
    {
        return $this->remoteId;
    }

    /**
     * @param string $language
     */
    public function getTranslatedName($language): ?string
    {
        $name = json_decode((string) $this->getName(), true);

        return is_array($name) ? ($name[$language] ?? null) : $this->getName();
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $language
     */
    public function getTranslatedUrl($language): ?string
    {
        $url = json_decode((string) $this->getUrl(), true);

        return is_array($url) ? ($url[$language] ?? null) : $this->getUrl();
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setTarget(string $target): void
    {
        $this->target = $target;
    }

    public function getMenu(): Menu
    {
        return $this->menu;
    }

    public function setMenu(Menu $menu): void
    {
        $this->menu = $menu;
    }

    public function hasChildrens(): bool
    {
        return !$this->childrens->isEmpty();
    }

    /**
     * @return MenuItem[]|ArrayCollection
     */
    public function getChildrens()
    {
        $criteria = new Criteria();
        $criteria->orderBy(['position' => Criteria::ASC]);

        return $this->childrens->matching($criteria);
    }

    /**
     * @param MenuItem[] $childrens
     */
    public function setChildrens($childrens): void
    {
        $this->childrens = new ArrayCollection(is_array($childrens) ? $childrens : $childrens->toArray());
    }

    public function addChildren(MenuItem $children): void
    {
        if (false === $this->childrens->indexOf($children)) {
            $children->setParent($this);
            $this->childrens->add($children);
        }
    }

    public function removeChildren(MenuItem $children): void
    {
        $this->childrens->removeElement($children);
    }

    public function getParent(): ?MenuItem
    {
        return $this->parent;
    }

    public function setParent(?MenuItem $parent): void
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

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getOptions(): array
    {
        return json_decode($this->options, true);
    }

    public function setOptions(array $options): void
    {
        $this->options = json_encode($options);
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getOption($name, $default = false)
    {
        $options = $this->getOptions();

        return $options[$name] ?? $default;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setOption($name, $value): void
    {
        $options = $this->getOptions();
        $options[$name] = $value;
        $this->setOptions($options);
    }

    public function __toString(): string
    {
        return (string) ($this->id ?? '');
    }

    public function update(array $properties): void
    {
        foreach ($properties as $property => $value) {
            if ($this->$property !== $value) {
                $this->$property = $value;
            }
        }
    }

    public function assignPositions(): void
    {
        /** @var MenuItem[] $childrens */
        $childrens = $this->getChildrens()->getValues();

        usort($childrens, fn(MenuItem $itemA, MenuItem $itemB) => $itemA->getPosition() <=> $itemB->getPosition());

        $position = 0;
        foreach ($childrens as $child) {
            $child->setPosition($position);
            $child->assignPositions();
            ++$position;
        }
    }
}