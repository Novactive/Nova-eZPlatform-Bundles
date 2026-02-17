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
#[ORM\Table(name: 'menu_manager_menu')]
class Menu implements \Stringable
{
    use IdentityTrait;

    #[ORM\Column(name: 'name', type: 'string', nullable: false)]
    protected string $name;

    #[ORM\Column(name: 'root_location_id', type: 'integer', nullable: true)]
    protected ?int $rootLocationId = null;

    #[ORM\OneToMany(
        targetEntity: MenuItem::class,
        mappedBy: 'menu',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    #[ORM\OrderBy(['position' => 'ASC'])]
    protected Collection $items;

    #[ORM\Column(name: 'type', type: 'string', nullable: true)]
    protected ?string $type = null;

    #[ORM\Column(name: 'remote_id', type: 'string', nullable: true)]
    protected string $remoteId;

    /**
     * Menu constructor.
     */
    public function __construct(?string $remoteId = null)
    {
        $this->items = new ArrayCollection();
        $this->remoteId = $remoteId ?? md5(uniqid(static::class, true));
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

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
     * @param MenuItem|null $parent
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
        $this->items = new ArrayCollection($items);
    }

    public function addItem(MenuItem $menuItem): void
    {
        if (false === $this->items->indexOf($menuItem)) {
            $menuItem->setMenu($this);
            $this->items->add($menuItem);
        }
    }

    public function removeItem(MenuItem $menuItem): void
    {
        $this->items->removeElement($menuItem);
    }

    public function getType(): ?string
    {
        return $this->type;
    }

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

    public function __toString(): string
    {
        return (string) ($this->id ?? '');
    }

    public function assignPositions(): void
    {
        /** @var MenuItem[] $childrens */
        $childrens = $this->items->filter(fn(MenuItem $item) => null === $item->getParent())->getValues();

        usort($childrens, fn(MenuItem $itemA, MenuItem $itemB) => $itemA->getPosition() <=> $itemB->getPosition());

        $position = 0;
        foreach ($childrens as $item) {
            $item->setPosition($position);
            $item->assignPositions();
            ++$position;
        }
    }
}