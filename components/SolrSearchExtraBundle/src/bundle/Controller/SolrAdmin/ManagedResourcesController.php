<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtraBundle\Controller\SolrAdmin;

use Novactive\EzSolrSearchExtra\Api\Schema\ManagedResourcesService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/solr/admin')]
class ManagedResourcesController extends BaseController
{
    protected const RESULTS_PER_PAGE = 20;

    public function __construct(protected ManagedResourcesService $managedResourcesService)
    {
    }

    #[Route('/', name: 'solr_admin.dashboard')]
    public function dashboardAction(): Response
    {
        $this->permissionAccess('solradmin', 'dashboard');
        $sets = $this->managedResourcesService->getSets();

        return $this->render('@ibexadesign/solr/admin/dashboard.html.Twig', [
            'sets' => $sets,
        ]);
    }
}
