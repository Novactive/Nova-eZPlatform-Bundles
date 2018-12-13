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

use Doctrine\ORM\EntityManagerInterface;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use Novactive\EzMenuManager\Form\Type\MenuDeleteType;
use Novactive\EzMenuManager\Form\Type\MenuType;
use Novactive\EzMenuManagerBundle\Entity\Menu;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

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

    /** @var TranslatorInterface */
    protected $translator;

    /** @var NotificationHandlerInterface */
    protected $notificationHandler;

    /**
     * AdminController constructor.
     *
     * @param TranslatorInterface                   $translator
     * @param NotificationHandlerInterface $notificationHandler
     */
    public function __construct(TranslatorInterface $translator, NotificationHandlerInterface $notificationHandler)
    {
        $this->translator          = $translator;
        $this->notificationHandler = $notificationHandler;
    }

    /**
     * @Route("/list/{page}", name="menu_manager.menu_list", requirements={"page" = "\d+"})
     *
     * @param EntityManagerInterface $em
     * @param int                    $page
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(EntityManagerInterface $em, $page = 1)
    {
        $queryBuilder = $em->createQueryBuilder()
                           ->select('m')
                           ->from(Menu::class, 'm');

        $pagerfanta = new Pagerfanta(
            new DoctrineORMAdapter($queryBuilder)
        );

        $pagerfanta->setMaxPerPage(self::RESULTS_PER_PAGE);
        $pagerfanta->setCurrentPage(min($page, $pagerfanta->getNbPages()));

        /** @var Menu[] $menus */
        $menus    = $pagerfanta->getCurrentPageResults();
        $menuIds  = $this->getMenusIds($menus);
        $formData = [
            'menus' => array_combine($menuIds, array_fill_keys($menuIds, false)),
        ];

        $menuDeleteForm = $this->createForm(MenuDeleteType::class, $formData);

        return $this->render(
            '@EzMenuManager/themes/standard/menu_manager/admin/list.html.twig',
            [
                'pager'            => $pagerfanta,
                'menu_delete_form' => $menuDeleteForm->createView(),
            ]
        );
    }

    /**
     * @param Menu[] $menus
     *
     * @return array
     */
    protected function getMenusIds($menus)
    {
        $ids = [];
        foreach ($menus as $menu) {
            $ids[] = $menu->getId();
        }

        return $ids;
    }

    /**
     * @Route("/new", name="menu_manager.menu_new")
     *
     * @param EntityManagerInterface $em
     * @param Request                $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(EntityManagerInterface $em, Request $request)
    {
        $menu = new Menu();

        return $this->editAction($em, $request, $menu);
    }

    /**
     * @Route("/edit/{menu}", name="menu_manager.menu_edit")
     *
     * @param EntityManagerInterface $em
     * @param Request                $request
     * @param Menu                   $menu
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(EntityManagerInterface $em, Request $request, Menu $menu)
    {
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Menu $menu */
            $menu = $form->getData();
            $em->persist($menu);
            $em->flush();

            $this->notificationHandler->success(
                $this->translator->trans('menu.notification.saved', [], 'menu_manager')
            );

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

    /**
     * @Route("/delete", name="menu_manager.menu_delete", methods={"POST"})
     *
     * @param Request $request
     * @param Menu    $menu
     */
    public function deleteAction(EntityManagerInterface $em, Request $request)
    {
        $form = $this->createForm(MenuDeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $formData = $form->getData();
            $menuIds  = array_keys($formData['menus']);
            foreach ($menuIds as $menuId) {
                $menu = $em->find(Menu::class, $menuId);
                $em->remove($menu);
            }
            $em->flush();

            $this->notificationHandler->success(
                $this->translator->trans('menu.notification.deleted', [], 'menu_manager')
            );
        }

        return $this->redirectToRoute('menu_manager.menu_list');
    }
}
