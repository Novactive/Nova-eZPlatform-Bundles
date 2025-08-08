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
use Ibexa\Bundle\Core\Controller;
use Ibexa\Contracts\Core\Persistence\Handler as PersistenceHandler;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Search\Handler as SearchHandler;
use Ibexa\Contracts\HttpCache\Handler\ContentTagInterface;
use Ibexa\Core\Repository\SiteAccessAware\Repository;
use Novactive\Bundle\eZProtectedContentBundle\Entity\ProtectedAccess;
use Novactive\Bundle\eZProtectedContentBundle\Form\ProtectedAccessType;
use Novactive\Bundle\eZProtectedContentBundle\Repository\ProtectedAccessRepository;
use Novactive\Bundle\eZProtectedContentBundle\Services\ObjectStateHelper;
use Novactive\Bundle\eZProtectedContentBundle\Services\ProtectedAccessHelper;
use Novactive\Bundle\eZProtectedContentBundle\Services\ReindexHelper;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class ProtectedAccessController extends Controller
{
    public function __construct(
        protected readonly Repository $repository,
        protected readonly SearchHandler $searchHandler,
        protected readonly PersistenceHandler $persistenceHandler,
        protected readonly ReindexHelper $reindexHelper,
        protected readonly ObjectStateHelper $objectStateHelper,
        protected readonly ProtectedAccessRepository $protectedAccessRepository,
        protected readonly ProtectedAccessHelper $protectedAccessHelper,
        protected readonly EntityManagerInterface $entityManager,
        protected readonly ContentTagInterface $responseTagger,
        protected readonly RouterInterface $router,
    ) { }

    #[Route(path: '/list', name: 'novaezprotectedcontent_bundle_admin_list_protection')]
    public function list(Request $request): ?Response
    {
        $page = $request->query->getInt('page', 1);
        $pageSize = $request->query->getInt('pageSize', 100);
        $offset = ($page - 1) * $pageSize;
        $offset = max(0, $offset);
        $list = $this->protectedAccessRepository->findAll($offset, $pageSize);

        $data = [];

        foreach ($list as $item) {
            /** @var ProtectedAccess $item */

            $count = $this->protectedAccessHelper->count($item);
            $content = $this->protectedAccessHelper->getContent($item);
            $data[$item->getId()] = [
                'ProtectedAccess' => $item,
                'count' => $count,
                'content' => $content,
            ];
        }

        return $this->render('@ibexadesign/list.html.twig', [
            'list' => $list,
            'data' => $data,
            'page_size' => $pageSize,
            'page' => $page,
        ]);
    }

    /**
     * @Route("/handle/{locationId}/{access}", name="novaezprotectedcontent_bundle_admin_handle_form",
     *                                           defaults={"accessId": null})
     */
    //#[Route(path: '/handle/{locationId}/{access}', name: 'novaezprotectedcontent_bundle_admin_handle_form')]
    public function handle(
        int $locationId,
        Request $request,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        ContentTagInterface $responseTagger,
        ?ProtectedAccess $access = null,
    ): RedirectResponse {
        if ($request->isMethod('post')) {
            $location = $this->repository->getLocationService()->loadLocation($locationId);
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
                $responseTagger->addLocationTags([$location->id]);
                $responseTagger->addParentLocationTags([$location->parentLocationId]);

                $content = $location->getContent();
                $this->objectStateHelper->setStatesForContentAndDescendants($content);
                $this->reindexHelper->reindexContent($content);
                if ($access->isProtectChildren()) {
                    $this->reindexHelper->reindexChildren($content);
                }
            }
        }

        return new RedirectResponse(
            $router->generate('ibexa.content.view', ['contentId' => $location->contentId,
                'locationId' => $location->id,
            ]).
            '#ibexa-tab-location-view-protect-content#tab'
        );
    }

    #[Route(path: '/remove/{locationId}/{access}', name: 'novaezprotectedcontent_bundle_admin_remove_protection')]
    public function remove(
        Location $location,
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        int $access,
        ContentTagInterface $responseTagger
    ): RedirectResponse {
        $access = $entityManager->find(ProtectedAccess::class, $access);
        $entityManager->remove($access);
        $entityManager->flush();
        $responseTagger->addLocationTags([$location->id]);
        $responseTagger->addParentLocationTags([$location->parentLocationId]);

        $content = $location->getContent();
        $this->objectStateHelper->setStatesForContentAndDescendants($content);
        $this->reindexHelper->reindexContent($content);
        if ($access->isProtectChildren()) {
            $this->reindexHelper->reindexChildren($content);
        }

        return new RedirectResponse(
            $router->generate('ibexa.content.view', ['contentId' => $location->contentId,
                'locationId' => $location->id,
            ]).
            '#ibexa-tab-location-view-protect-content#tab'
        );
    }

    #[Route(path: '/delete/{accessId}', name: 'novaezprotectedcontent_bundle_admin_delete_protection')]
    public function delete(
        EntityManagerInterface $entityManager,
        int $accessId,
        ContentTagInterface $responseTagger
    ): RedirectResponse {
        $access = $this->entityManager->find(ProtectedAccess::class, $accessId);
        $entityManager->remove($access);
        $entityManager->flush();
        return new RedirectResponse(
            $this->router->generate('novaezprotectedcontent_bundle_admin_list_protection')
        );
    }

}
