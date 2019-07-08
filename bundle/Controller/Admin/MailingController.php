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

use Doctrine\ORM\EntityManagerInterface;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\Helper\TranslationHelper;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Tab\LocationView\ContentTab;
use EzSystems\EzPlatformAdminUi\UI\Module\Subitems\ContentViewParameterSupplier;
use Novactive\Bundle\eZMailingBundle\Core\Processor\TestMailingProcessorInterface as TestMailing;
use Novactive\Bundle\eZMailingBundle\Entity\Campaign;
use Novactive\Bundle\eZMailingBundle\Entity\Mailing;
use Novactive\Bundle\eZMailingBundle\Entity\User;
use Novactive\Bundle\eZMailingBundle\Form\MailingType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormFactoryInterface;
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
     * @param Mailing                      $mailing
     * @param ContentViewParameterSupplier $contentViewParameterSupplier
     *
     * @return array
     */
    public function showAction(
        Mailing $mailing,
        ContentViewParameterSupplier $contentViewParameterSupplier,
        FormFactory $formFactory
    ): array {
        $contentView = new ContentView();
        $contentView->setLocation($mailing->getLocation());
        $contentView->setContent($mailing->getContent());
        $contentViewParameterSupplier->supply($contentView);

        $subitemsContentEdit = $formFactory->contentEdit(
            null,
            'form_subitems_content_edit'
        );

        return [
            'item'                       => $mailing,
            'form_subitems_content_edit' => $subitemsContentEdit->createView(),
            'subitems_module'            => $contentView->getParameter('subitems_module'),
        ];
    }

    /**
     * @Template()
     * @Security("is_granted('view', mailing)")
     *
     * @param Mailing    $mailing
     * @param Repository $repository
     * @param ContentTab $contentTab
     *
     * @return array
     */
    public function mailingTabsAction(
        Mailing $mailing,
        Repository $repository,
        ContentTab $contentTab,
        EntityManagerInterface $entityManager
    ): array {
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
            'item'            => $mailing,
            'totalRecipients' => $entityManager->getRepository(User::class)->countValidRecipients(
                $mailing->getCampaign()->getMailingLists()
            ),
            'preview'         => $preview,
        ];
    }

    /**
     * @Route("/edit/{mailing}", name="novaezmailing_mailing_edit")
     * @ParamConverter("mailing", class="Novactive\Bundle\eZMailingBundle\Entity\Mailing", options={"id"="mailing"})
     * @Route("/create/{campaign}", name="novaezmailing_mailing_create")
     * @ParamConverter("campaign", class="Novactive\Bundle\eZMailingBundle\Entity\Campaign", options={"id"="campaign"})
     * @Template()
     * @Security("is_granted('edit', mailing)")
     *
     * @param Mailing|null           $mailing
     * @param Campaign|null          $campaign
     * @param Request                $request
     * @param RouterInterface        $router
     * @param FormFactoryInterface   $formFactory
     * @param EntityManagerInterface $entityManager
     * @param Registry               $workflows
     * @param TranslationHelper      $translationHelper
     *
     * @return array|RedirectResponse
     */
    public function editAction(
        ?Mailing $mailing,
        ?Campaign $campaign,
        Request $request,
        RouterInterface $router,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        Registry $workflows,
        TranslationHelper $translationHelper,
        Repository $repository
    ) {
        if (null === $mailing) {
            $mailing = new Mailing();
            $mailing
                ->setStatus(Mailing::DRAFT)
                ->setCampaign($campaign);
            $languages = array_filter($translationHelper->getAvailableLanguages());
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
            $mailing->setUpdated(new \DateTime());
            $entityManager->persist($mailing);
            $entityManager->flush();

            return new RedirectResponse(
                $router->generate('novaezmailing_mailing_show', ['mailing' => $mailing->getId()])
            );
        }

        if (null !== $mailing->getLocationId()) {
            $location = $repository->getLocationService()->loadLocation($mailing->getLocationId());
            $content  = $repository->getContentService()->loadContentByContentInfo($location->contentInfo);
            $mailing->setLocation($location);
            $mailing->setContent($content);
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
     * @param Request                $request
     * @param Mailing                $mailing
     * @param RouterInterface        $router
     * @param EntityManagerInterface $entityManager
     * @param Registry               $workflows
     *
     * @return RedirectResponse
     */
    public function statusAction(
        Request $request,
        Mailing $mailing,
        RouterInterface $router,
        EntityManagerInterface $entityManager,
        Registry $workflows
    ): RedirectResponse {
        $action  = substr($request->get('_route'), \strlen('novaezmailing_mailing_'));
        $machine = $workflows->get($mailing);
        $machine->apply($mailing, $action);
        $entityManager->flush();

        return new RedirectResponse($router->generate('novaezmailing_mailing_show', ['mailing' => $mailing->getId()]));
    }

    /**
     * @Route("/test/{mailing}", name="novaezmailing_mailing_test")
     * @Security("is_granted('view', mailing)")
     * @Method({"POST"})
     *
     * @param Request                $request
     * @param Mailing                $mailing
     * @param TestMailing            $processor
     * @param RouterInterface        $router
     * @param EntityManagerInterface $entityManager
     * @param Registry               $workflows
     *
     * @return RedirectResponse
     */
    public function testAction(
        Request $request,
        Mailing $mailing,
        TestMailing $processor,
        RouterInterface $router,
        EntityManagerInterface $entityManager,
        Registry $workflows
    ): RedirectResponse {
        $machine = $workflows->get($mailing);
        if ($machine->can($mailing, 'test')) {
            $ccEmail = $request->request->get('cc');
            if (\strlen($ccEmail) > 0) {
                $processor->execute($mailing, $ccEmail);
                $machine->apply($mailing, 'test');
                $entityManager->flush();
            }
        }

        return new RedirectResponse($router->generate('novaezmailing_mailing_show', ['mailing' => $mailing->getId()]));
    }
}
