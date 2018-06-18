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

namespace Novactive\EzMenuManagerBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Novactive\EzMenuManager\Form\Type\MenuEditType;
use Novactive\EzMenuManagerBundle\Entity\Menu;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminController.
 *
 * @Route("/menu-manager/admin")
 *
 * @package Novactive\EzMenuManagerBundle\Controller
 */
class AdminController extends Controller
{
    const RESULTS_PER_PAGE = 10;

    /**
     * @Route("/list/{page}", name="menu_manager.menu_list", requirements={"page" = "\d+"})
     *
     * @param EntityManager $em
     * @param int           $page
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(EntityManagerInterface $em, $page = 1)
    {
        $queryBuilder = $em->createQueryBuilder()
                           ->select('m')
                           ->from(Menu::class, 'm');

        $paginator = new Pagerfanta(
            new DoctrineORMAdapter($queryBuilder)
        );
        $paginator->setMaxPerPage(self::RESULTS_PER_PAGE);
        $paginator->setCurrentPage($page);

        return $this->render(
            '@EzMenuManager/themes/standard/menu_manager/admin/list.html.twig',
            [
                'totalCount' => $paginator->getNbResults(),
                'menus'      => $paginator,
            ]
        );
    }

    /**
     * @Route("/new", name="menu_manager.menu_new")
     *
     * @param EntityManagerInterface $em
     * @param Request                $request
     */
    public function newAction(EntityManagerInterface $em, Request $request)
    {
        $menu = new Menu();

        return $this->editAction($em, $request, $menu);
    }

    /**
     * @Route("/edit/{menu}", name="menu_manager.menu_edit")
     *
     * @param Menu $menu
     */
    public function editAction(EntityManagerInterface $em, Request $request, Menu $menu)
    {
        $form = $this->createForm(MenuEditType::class, $menu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($menu);
            $em->flush();

            return $this->redirectToRoute('menu_manager.menu_list');
        }

        return $this->render(
            '@EzMenuManager/themes/standard/menu_manager/admin/edit.html.twig',
            [
                'form'  => $form->createView(),
                'title' => $menu->getId() ? $menu->getName() : 'menu.new',
            ]
        );
    }
}
