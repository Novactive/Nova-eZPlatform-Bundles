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

namespace Novactive\EzMenuManagerBundle\Controller;

use Ibexa\Contracts\AdminUi\Controller\Controller;
use Novactive\EzMenuManager\Service\MenuBuilder;
use Novactive\EzMenuManagerBundle\Entity\Menu;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ViewController.
 *
 * @Route("/menu-manager")
 *
 * @package Novactive\EzMenuManagerBundle\Controller
 */
class ViewController extends Controller
{
    /**
     * @Route("/view/{menu}", name="menu_manager.menu_view")
     */
    public function viewMenuAction(Menu $menu, MenuBuilder $menuBuilder): Response
    {
        return $this->render(
            '@ibexadesign/menu_manager/view.html.twig',
            [
                'knpMenu' => $menuBuilder->build($menu),
                'title' => $menu->getName(),
            ]
        );
    }
}
