<?php

/**
 * NovaeZSolrSearchExtraBundle.
 *
 * @package   NovaeZSolrSearchExtraBundle
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZSolrSearchExtraBundle/blob/master/LICENSE
 */

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
    protected const RESULTS_PER_PAGE = 20;

    /** @var ManagedResourcesService */
    protected $managedResourcesService;

    /**
     * ManagedResourcesController constructor.
     */
    public function __construct(ManagedResourcesService $managedResourcesService)
    {
        $this->managedResourcesService = $managedResourcesService;
    }

    /**
     * @Route("/", name="solr_admin.dashboard")
     * @Template("@ezdesign/solr/admin/dashboard.html.Twig")
     */
    public function dashboardAction()
    {
        $this->permissionAccess('solradmin', 'dashboard');
        $sets = $this->managedResourcesService->getSets();

        return [
            'sets' => $sets,
        ];
    }

    protected function getTermsData($terms)
    {
        $ids = [];
        foreach ($terms as $term) {
            $ids[] = $term->getTerm();
        }

        return $ids;
    }
}
