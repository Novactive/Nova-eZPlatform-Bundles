<?php
/**
 * NovaeZProtectedContentBundle.
 *
 * @package   Novactive\Bundle\eZProtectedContentBundle
 *
 * @author    Novactive
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/eZProtectedContentBundle/blob/master/LICENSE MIT Licence
 */
declare(strict_types=1);

namespace Novactive\Bundle\eZProtectedContentBundle\Controller\Admin;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use eZ\Publish\API\Repository\Values\Content\Location;
use Novactive\Bundle\eZProtectedContentBundle\Entity\ProtectedAccess;
use Novactive\Bundle\eZProtectedContentBundle\Form\ProtectedAccessType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class ProtectedAccessController
{
    /**
     * @Route("/handle/{locationId}/{access}", name="novaezprotectedcontent_bundle_admin_handle_form",
     *                                           defaults={"accessId": null})
     */
    public function handle(
        Location $location,
        Request $request,
        FormFactory $formFactory,
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        ?ProtectedAccess $access = null
    ): RedirectResponse {
        if ($request->isMethod('post')) {
            $now = new DateTime();
            if (null === $access) {
                $access = new ProtectedAccess();
                $access->setCreated($now);
            }
            $form = $formFactory->create(ProtectedAccessType::class, $access);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $access->setUpdated($now);
                $entityManager->persist($access);
                $entityManager->flush();
            }
        }

        return new RedirectResponse($router->generate($location).'#ez-tab-location-view-protect-content#tab');
    }

    /**
     * @Route("/remove/{locationId}/{access}", name="novaezprotectedcontent_bundle_admin_remove_protection")
     */
    public function remove(
        Location $location,
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        ProtectedAccess $access
    ): RedirectResponse {
        $entityManager->remove($access);
        $entityManager->flush();

        return new RedirectResponse($router->generate($location).'#ez-tab-location-view-protect-content#tab');
    }
}
