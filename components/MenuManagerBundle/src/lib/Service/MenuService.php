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

namespace Novactive\EzMenuManager\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Location;
use Novactive\EzMenuManager\MenuItem\MenuItemTypeRegistry;
use Novactive\EzMenuManagerBundle\Entity\Menu;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;

class MenuService
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var LocationService */
    protected $locationService;

    /** @var MenuItemTypeRegistry */
    protected $menuItemTypeRegistry;

    /**
     * MenuService constructor.
     *
     * @param EntityManagerInterface $em
     * @param LocationService        $locationService
     * @param MenuItemTypeRegistry   $menuItemTypeRegistry
     */
    public function __construct(
        EntityManagerInterface $em,
        LocationService $locationService,
        MenuItemTypeRegistry $menuItemTypeRegistry
    ) {
        $this->em                   = $em;
        $this->locationService      = $locationService;
        $this->menuItemTypeRegistry = $menuItemTypeRegistry;
    }

    /**
     * @param $locationId
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     *
     * @return Menu[]
     */
    public function getAvailableMenuForLocationId($locationId)
    {
        $location = $this->locationService->loadLocation($locationId);

        $qb = $this->em->createQueryBuilder();
        $qb->select('m');
        $qb->from('EzMenuManagerBundle:Menu', 'm');
        $qb->where($qb->expr()->in('m.rootLocationId', $location->path));
        $qb->orWhere($qb->expr()->isNull('m.rootLocationId'));

        return $qb->getQuery()->execute();
    }

    /**
     * @param Location $location
     * @param Menu     $menu
     *
     * @return MenuItem[]|ArrayCollection
     */
    public function getLocationMenuItemsInMenu(Location $location, Menu $menu)
    {
        $criteria = new Criteria();
        $criteria->where(Criteria::expr()->eq('url', MenuItem\ContentMenuItem::URL_PREFIX.$location->contentId));

        return $menu->getItems()->matching($criteria);
    }

    /**
     * @param $menuId
     *
     * @return Menu|object|null
     */
    public function loadMenu($menuId)
    {
        return $this->em->getRepository(Menu::class)->find($menuId);
    }
}
