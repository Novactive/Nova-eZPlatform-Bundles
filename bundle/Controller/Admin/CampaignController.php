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
use EzSystems\EzPlatformAdminUi\Tab\LocationView\ContentTab;
use Novactive\Bundle\eZMailingBundle\Core\Provider\User as UserProvider;
use Novactive\Bundle\eZMailingBundle\Entity\Campaign;
use Novactive\Bundle\eZMailingBundle\Entity\Mailing;
use Novactive\Bundle\eZMailingBundle\Form\CampaignType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class CampaignController.
 *
 * @Route("/campaign")
 */
class CampaignController
{
    /**
     * @Template()
     * @Security("is_granted('view', campaign)")
     *
     * @return array
     */
    public function campaignTabsAction(
        Campaign $campaign,
        string $status = 'all',
        Repository $repository,
        ContentTab $contentTab,
        EntityManagerInterface $entityManager
    ): array {
        $content = $campaign->getContent();
        if (null !== $content) {
            $contentType = $repository->getContentTypeService()->loadContentType(
                $content->contentInfo->contentTypeId
            );
            $preview     = $contentTab->renderView(
                [
                    'content'     => $content,
                    'location'    => $campaign->getLocation(),
                    'contentType' => $contentType,
                ]
            );
        }
        $repo     = $entityManager->getRepository(Mailing::class);
        $mailings = $repo->findByFilters(
            [
                'campaign' => $campaign,
                'status'   => 'all' === $status ? null : $status,
            ]
        );

        return [
            'item'     => $campaign,
            'status'   => $status,
            'children' => $mailings,
            'preview'  => $preview ?? null,
        ];
    }

    /**
     * @Route("/show/subscriptions/{campaign}/{status}/{page}/{limit}", name="novaezmailing_campaign_subscriptions",
     *                                              defaults={"page":1, "limit":10, "status":"all"})
     * @Template()
     * @Security("is_granted('view', campaign)")
     *
     * @return array
     */
    public function subscriptionsAction(
        Campaign $campaign,
        UserProvider $provider,
        string $status = 'all',
        int $page = 1,
        int $limit = 10
    ): array {
        $filers = [
            'campaign' => $campaign,
            'status'   => 'all' === $status ? null : $status,
        ];

        return [
            'pager'         => $provider->getPagerFilters($filers, $page, $limit),
            'statuses'      => $provider->getStatusesData($filers),
            'currentStatus' => $status,
            'item'          => $campaign,
        ];
    }

    /**
     * @Route("/show/mailings/{campaign}/{status}", name="novaezmailing_campaign_mailings")
     * @Template()
     * @Security("is_granted('view', campaign)")
     *
     * @param Campaign               $campaign
     * @param EntityManagerInterface $entityManager
     * @param string                 $status
     *
     * @return array
     */
    public function mailingsAction(Campaign $campaign, EntityManagerInterface $entityManager, string $status): array
    {
        $repo    = $entityManager->getRepository(Mailing::class);
        $results = $repo->findByFilters(
            [
                'campaign' => $campaign,
                'status'   => $status,
            ]
        );

        return [
            'item'     => $campaign,
            'status'   => $status,
            'children' => $results,
        ];
    }

    /**
     * @Route("/edit/{campaign}", name="novaezmailing_campaign_edit")
     * @Route("/create", name="novaezmailing_campaign_create")
     * @Security("is_granted('edit', campaign)")
     * @Template()
     *
     * @param Campaign|null          $campaign
     * @param Request                $request
     * @param RouterInterface        $router
     * @param EntityManagerInterface $entityManager
     * @param FormFactoryInterface   $formFactory
     * @param TranslationHelper      $translationHelper
     *
     * @return array|RedirectResponse
     */
    public function editAction(
        ?Campaign $campaign,
        Request $request,
        RouterInterface $router,
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        TranslationHelper $translationHelper
    ) {
        if (null === $campaign) {
            $campaign  = new Campaign();
            $languages = array_filter($translationHelper->getAvailableLanguages());
            $campaign->setNames(array_combine($languages, array_pad([], count($languages), '')));
        }

        $form = $formFactory->create(CampaignType::class, $campaign);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($campaign);
            $entityManager->flush();

            return new RedirectResponse(
                $router->generate('novaezmailing_campaign_subscriptions', ['campaign' => $campaign->getId()])
            );
        }

        return [
            'item' => $campaign,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/delete/{campaign}", name="novaezmailing_campaign_remove")
     * @Security("is_granted('edit', campaign)")
     *
     * @param Campaign               $campaign
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface        $router
     *
     * @return RedirectResponse
     */
    public function deleteAction(
        Campaign $campaign,
        EntityManagerInterface $entityManager,
        RouterInterface $router
    ): RedirectResponse {
        $entityManager->remove($campaign);
        $entityManager->flush();

        return new RedirectResponse($router->generate('novaezmailing_dashboard_index'));
    }
}
