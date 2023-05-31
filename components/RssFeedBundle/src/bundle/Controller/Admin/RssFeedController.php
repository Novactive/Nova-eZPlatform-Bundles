<?php

/**
 * NovaeZRssFeedBundle.
 *
 * @package   NovaeZRssFeedBundle
 *
 * @author    Novactive
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZRssFeedBundle/blob/master/LICENSE
 */

namespace Novactive\EzRssFeedBundle\Controller\Admin;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use eZ\Publish\API\Repository\PermissionResolver;
use Ibexa\Bundle\Core\Controller;
use Ibexa\Contracts\AdminUi\Notification\NotificationHandlerInterface;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Novactive\EzRssFeedBundle\Controller\EntityManagerTrait;
use Novactive\EzRssFeedBundle\Entity\RssFeeds;
use Novactive\EzRssFeedBundle\Form\RssFeedsType;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/rssfeeds")
 *
 * Class RssFeedController
 *
 * @package Novactive\EzRssFeedBundle\Controller
 */
class RssFeedController extends Controller
{
    use EntityManagerTrait;

    private $defaultPaginationLimit = 10;

    private $notificationHandler;

    public function __construct(NotificationHandlerInterface $notificationHandler)
    {
        $this->notificationHandler = $notificationHandler;
    }

    /**
     * @Route("/", name="platform_admin_ui_rss_feeds_list")
     */
    public function listAction(Request $request): Response
    {
        $rssFeedRepository = $this->entityManager->getRepository(RssFeeds::class);

        /**
         * @var PermissionResolver
         */
        $permissionResolver = $this->container->get('ibexa.api.repository')->getPermissionResolver();

        $page = $request->query->get('page') ?? 1;

        $pagerfanta = new Pagerfanta(
            new ArrayAdapter($rssFeedRepository->findAll())
        );

        $pagerfanta->setMaxPerPage($this->defaultPaginationLimit);
        $pagerfanta->setCurrentPage(min($page, $pagerfanta->getNbPages()));

        return $this->render(
            '@ibexadesign/rssfeed/list.html.twig',
            [
                'pager' => $pagerfanta,
                'canCreate' => $permissionResolver->hasAccess('rss', 'create'),
                'canDelete' => $permissionResolver->hasAccess('rss', 'delete'),
            ]
        );
    }

    /**
     * @Route("/add", name="platform_admin_ui_rss_feeds_create")
     */
    public function createAction(Request $request): Response
    {
        /**
         * @var PermissionResolver
         */
        $permissionResolver = $this->getRepository()->getPermissionResolver();

        if (!$permissionResolver->hasAccess('rss', 'create')) {
            throw new UnauthorizedException('rss', 'create', []);
        }

        $rssFeed = new RssFeeds();
        $form = $this->createForm(RssFeedsType::class, $rssFeed);

        $form->add(
            'submit',
            SubmitType::class,
            [
                'label' => 'Create',
                'attr' => [
                    'class' => 'btn btn-default pull-right',
                    'id' => 'rss_edit_edit',
                ],
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rssFeed->setStatus(RssFeeds::STATUS_ENABLED);
            $this->entityManager->persist($rssFeed);
            $this->entityManager->flush();

            return $this->redirectToRoute('platform_admin_ui_rss_feeds_list');
        }

        return $this->render(
            '@ibexadesign/rssfeed/edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/edit/{id}", name="platform_admin_ui_rss_feeds_edit")
     * @ParamConverter("rssFeed", class="Novactive\EzRssFeedBundle\Entity\RssFeeds")
     */
    public function editAction(Request $request, RssFeeds $rssFeed): Response
    {
        /**
         * @var PermissionResolver
         */
        $permissionResolver = $this->getRepository()->getPermissionResolver();

        if (!$permissionResolver->hasAccess('rss', 'edit')) {
            throw new UnauthorizedException('rss', 'edit', []);
        }
        $originalSites = new ArrayCollection();
        foreach ($rssFeed->getFeedSites() as $site) {
            $originalSites->add($site);
        }
        $originalFeedsItems = new ArrayCollection();
        foreach ($rssFeed->getFeedItems() as $item) {
            $originalFeedsItems->add($item);
        }
        $feedForm = $this->createForm(RssFeedsType::class, $rssFeed);
        $feedForm->add(
            'submit',
            SubmitType::class,
            [
                'label' => 'Create',
                'attr' => [
                    'class' => 'btn-secondary btn',
                ],
            ]
        );
        $feedForm->handleRequest($request);

        if ($feedForm->isSubmitted() && $feedForm->isValid()) {
            $rssFeed->setModifiedAt(new DateTime());
            foreach ($rssFeed->getFeedItems() as $feedItem) {
                $feedItem->setModifiedAt(new DateTime());
            }
            $this->entityManager->persist($rssFeed);

            foreach ($originalFeedsItems as $originalChild) {
                if (false === $rssFeed->getFeedItems()->contains($originalChild)) {
                    $rssFeed->removeFeedItem($originalChild);
                    $originalChild->setRssFeeds(null);
                    $this->entityManager->remove($originalChild);
                }
            }
             foreach ($originalSites as $originalChild) {
                if (false === $rssFeed->getFeedSites()->contains($originalChild)) {
                    $rssFeed->removeFeedSite($originalChild);
                    $originalChild->setRssFeeds(null);
                    $this->entityManager->remove($originalChild);
                }
            }

            $this->entityManager->flush();

            $this->getNotificationHandler()->success('Mise à jour effectuée avec succès.');

            return new RedirectResponse($this->generateUrl('platform_admin_ui_rss_feeds_list'));
        }

        return $this->render(
            '@ibexadesign/rssfeed/edit.html.twig',
            [
                'form' => $feedForm->createView(),
            ]
        );
    }

    public function getNotificationHandler(): NotificationHandlerInterface
    {
        return $this->notificationHandler;
    }

    /**
     * @Route("/delete/{id}", name="platform_admin_ui_rss_feeds_delete")
     * @ParamConverter("rssFeed", class="Novactive\EzRssFeedBundle\Entity\RssFeeds")
     */
    public function deleteAction(Request $request, RssFeeds $rssFeed): RedirectResponse
    {
        /**
         * @var PermissionResolver
         */
        $permissionResolver = $this->getRepository()->getPermissionResolver();

        if (!$permissionResolver->hasAccess('rss', 'delete')) {
            throw new UnauthorizedException('rss', 'delete', []);
        }

        if ($request->request) {
            $this->entityManager->remove($rssFeed);
            $this->entityManager->flush();
        }

        return new RedirectResponse($this->generateUrl('platform_admin_ui_rss_feeds_list'));
    }

    /**
     * @Route("/rss_feed/ajx/location/{locationId}", name="platform_admin_ui_rss_feeds_ajx_load_location")
     */
    public function loadLocationAjaxAction(Request $request, $locationId = null): Response
    {
        /**
         * @var PermissionResolver
         */
        $permissionResolver = $this->getRepository()->getPermissionResolver();

        if (!$permissionResolver->hasAccess('rss', 'edit')) {
            throw new UnauthorizedException('rss', 'edit', []);
        }

        $data = [];

        if ($request->get('locationId')) {
            $locationId = $request->get('locationId');
        }

        if ($locationId) {
            $repository = $this->getRepository();
            $locationService = $repository->getLocationService();
            $location = $locationService->loadLocation($locationId);

            $data = [
                'location' => $locationId,
                'content' => [
                    'id' => $location->contentInfo->id,
                    'name' => $location->contentInfo->name,
                ],
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/edit/ajax/get_rss_field_by_contenttype_id", name="platform_admin_ui_rss_ajax_get_fields_by_contenttype_id")
     */
    public function getAjaxFieldByContentTypeIdAction(Request $request): JsonResponse
    {
        /**
         * @var PermissionResolver
         */
        $permissionResolver = $this->getRepository()->getPermissionResolver();

        if (!$permissionResolver->hasAccess('rss', 'edit')) {
            throw new UnauthorizedException('rss', 'edit', []);
        }

        $fieldsMap = [];

        if ($request->get('contenttype_id')) {
            $contentType = $this->getRepository()
                                ->getContentTypeService()
                                ->loadContentType($request->get('contenttype_id'));

            foreach ($contentType->getFieldDefinitions() as $fieldDefinition) {
                $fieldsMap[ucfirst($fieldDefinition->getName())] =
                    $fieldDefinition->identifier;
            }

            ksort($fieldsMap);
        }

        return new JsonResponse($fieldsMap);
    }

    /**
     * @Route("/ajax/change_visibility_feed", methods={"POST"},
     *                                        name="platform_admin_ui_rss_ajax_change_visibility_feed")
     */
    public function changeAjaxVisibilityFeed(Request $request): JsonResponse
    {
        /**
         * @var PermissionResolver
         */
        $permissionResolver = $this->getRepository()->getPermissionResolver();

        if (!$permissionResolver->hasAccess('rss', 'edit')) {
            throw new UnauthorizedException('rss', 'edit', []);
        }
        $repository = $this->entityManager->getRepository(RssFeeds::class);

        /**
         * @var RssFeeds
         */
        $rssFeed = $repository->find($request->get('feedId'));

        if (!empty($rssFeed)) {
            $status = RssFeeds::STATUS_ENABLED === $rssFeed->getStatus() ?
                RssFeeds::STATUS_DISABLED : RssFeeds::STATUS_ENABLED;

            $rssFeed->setStatus($status);
            $this->entityManager->persist($rssFeed);
            $this->entityManager->flush();

            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(['success' => false], 404);
    }
}
