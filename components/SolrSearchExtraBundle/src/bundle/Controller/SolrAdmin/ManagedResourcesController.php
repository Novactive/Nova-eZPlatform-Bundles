<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtraBundle\Controller\SolrAdmin;

use Novactive\EzSolrSearchExtra\Api\Schema\ManagedResourcesService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController.
 *
 * @Route("/solr/admin")
 */
class ManagedResourcesController extends BaseController
{
    protected const int RESULTS_PER_PAGE = 20;
    
    /**
     * ManagedResourcesController constructor.
     */
    public function __construct(protected ManagedResourcesService $managedResourcesService)
    {
    }

    /**
     * @Route("/", name="solr_admin.dashboard")
     * @Template("@ibexadesign/solr/admin/dashboard.html.Twig")
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Exception
     */
    public function dashboardAction(): array
    {
        $this->permissionAccess('solradmin', 'dashboard');
        $sets = $this->managedResourcesService->getSets();

        return [
            'sets' => $sets,
        ];
    }

    /**
     * @param $terms
     */
    protected function getTermsData($terms): array
    {
        $ids = [];
        foreach ($terms as $term) {
            $ids[] = $term->getTerm();
        }

        return $ids;
    }
}
