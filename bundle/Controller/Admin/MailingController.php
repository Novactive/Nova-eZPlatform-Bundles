<?php
/**
 * NovaeZMailingBundle Bundle.
 *
 * @package   Novactive\Bundle\eZMailingBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMailingBundle/blob/master/LICENSE MIT Licence
 */
declare(strict_types=1);

namespace Novactive\Bundle\eZMailingBundle\Controller\Admin;

use Doctrine\ORM\EntityManager;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\Helper\TranslationHelper;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use EzSystems\EzPlatformAdminUi\Tab\LocationView\ContentTab;
use EzSystems\EzPlatformAdminUi\UI\Module\Subitems\ContentViewParameterSupplier;
use Novactive\Bundle\eZMailingBundle\Entity\Campaign;
use Novactive\Bundle\eZMailingBundle\Entity\Mailing;
use Novactive\Bundle\eZMailingBundle\Form\MailingType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Workflow\Registry;

/**
 * Class MailingController.
 *
 * @Route("/mailing")
 */
class MailingController
{
    /**
     * @Route("/show/{mailing}", name="novaezmailing_mailing_show")
     * @Template()
     * @Security("is_granted('view', mailing)")
     *
     * @return array
     */
    public function showAction(Mailing $mailing, ContentViewParameterSupplier $contentViewParameterSupplier): array
    {
        $contentView = new ContentView();
        $contentView->setLocation($mailing->getLocation());
        $contentViewParameterSupplier->supply($contentView);

        return [
            'item'            => $mailing,
            'subitems_module' => $contentView->getParameter('subitems_module'),
        ];
    }

    /**
     * @Template()
     * @Security("is_granted('view', mailing)")
     *
     * @return array
     */
    public function mailingTabsAction(Mailing $mailing, Repository $repository, ContentTab $contentTab): array
    {
        $content     = $mailing->getContent();
        $contentType = $repository->getContentTypeService()->loadContentType(
            $content->contentInfo->contentTypeId
        );
        $preview     = $contentTab->renderView(
            [
                'content'     => $content,
                'location'    => $mailing->getLocation(),
                'contentType' => $contentType,
            ]
        );

        return [
            'item'    => $mailing,
            'preview' => $preview,
        ];
    }

    /**
     * @Route("/edit/{mailing}", name="novaezmailing_mailing_edit")
     * @Route("/create/{campaign}", name="novaezmailing_mailing_create")
     * @Template()
     * @Security("is_granted('edit', mailing)")
     *
     * @return array|RedirectResponse
     */
    public function editAction(
        ?Mailing $mailing,
        ?Campaign $campaign,
        Request $request,
        RouterInterface $router,
        FormFactoryInterface $formFactory,
        EntityManager $entityManager,
        Registry $workflows,
        TranslationHelper $translationHelper
    ) {
        if (null === $mailing) {
            $mailing = new Mailing();
            $mailing->setStatus(Mailing::DRAFT);
            $mailing->setCampaign($campaign);
            $languages = $translationHelper->getAvailableLanguages();
            $mailing->setNames(array_combine($languages, array_pad([], count($languages), '')));
        }

        $machine = $workflows->get($mailing);
        if (!$machine->can($mailing, 'edit')) {
            throw new AccessDeniedHttpException('Not Allowed');
        }

        $form = $formFactory->create(MailingType::class, $mailing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $machine->apply($mailing, 'edit');
            $entityManager->persist($mailing);
            $entityManager->flush();

            return new RedirectResponse(
                $router->generate('novaezmailing_mailing_show', ['mailing' => $mailing->getId()])
            );
        }

        return [
            'item' => $mailing,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/confirm/{mailing}", name="novaezmailing_mailing_confirm")
     * @Route("/archive/{mailing}", name="novaezmailing_mailing_archive")
     * @Route("/abort/{mailing}",   name="novaezmailing_mailing_cancel")
     * @Security("is_granted('view', mailing)")
     *
     * @return JsonResponse
     */
    public function confirmAction(
        Request $request,
        Mailing $mailing,
        RouterInterface $router,
        EntityManager $entityManager,
        Registry $workflows
    ): RedirectResponse {
        $action  = substr($request->get('_route'), \strlen('novaezmailing_mailing_'));
        $machine = $workflows->get($mailing);
        $machine->apply($mailing, $action);
        $entityManager->flush();

        return new RedirectResponse($router->generate('novaezmailing_mailing_show', ['mailing' => $mailing->getId()]));
    }
}
