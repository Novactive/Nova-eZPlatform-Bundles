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

namespace Novactive\EzMenuManager\Service;

use Doctrine\ORM\EntityManagerInterface;
use eZ\Publish\API\Repository\LocationService;
use Novactive\EzMenuManagerBundle\Entity\Menu;
use Novactive\EzMenuManagerBundle\Entity\MenuItem;

class MenuService
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var LocationService */
    protected $locationService;

    /**
     * MenuService constructor.
     *
     * @param EntityManagerInterface $em
     * @param LocationService        $locationService
     */
    public function __construct(EntityManagerInterface $em, LocationService $locationService)
    {
        $this->em              = $em;
        $this->locationService = $locationService;
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
     * @param Menu $menu
     * @param $locationId
     *
     * @return array
     */
    public function getMenuItemsInMenuWithLocationId(Menu $menu, $locationId)
    {
        $ContentMenuItems = [];
        foreach ($menu->getItems() as $item) {
            $ContentMenuItems += $this->getMenuItemsInMenuItemChildrensWithLocationId($item, $locationId);
        }

        return $ContentMenuItems;
    }

    /**
     * @param MenuItem $menuItem
     * @param $locationId
     *
     * @return array
     */
    public function getMenuItemsInMenuItemChildrensWithLocationId(MenuItem $menuItem, $locationId)
    {
        $ContentMenuItems = [];
        foreach ($menuItem->getChildrens() as $item) {
            $ContentMenuItems += $this->getMenuItemsInMenuItemChildrensWithLocationId($item, $locationId);
        }

        return $ContentMenuItems;
    }
}
