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

use Novactive\EzSolrSearchExtra\Api\Schema\Analysis\Synonyms\SynonymsMap;
use Novactive\EzSolrSearchExtra\Api\Schema\Analysis\Synonyms\SynonymsService;
use Novactive\EzSolrSearchExtra\Form\AddSynonymsType;
use Novactive\EzSolrSearchExtra\Pagination\Pagerfanta\SynonymsAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SynonymsController.
 *
 * @package Novactive\EzSolrSearchExtraBundle\Controller\SolrAdmin
 *
 * @Route("/solr/admin/synonyms")
 */
class SynonymsController extends BaseController
{
    protected const RESULTS_PER_PAGE = 20;

    /** @var SynonymsService */
    protected $synonymsService;

    /** @var FormFactory */
    protected $formFactory;

    /**
     * SynonymsController constructor.
     */
    public function __construct(SynonymsService $synonymsService, FormFactory $formFactory)
    {
        $this->synonymsService = $synonymsService;
        $this->formFactory     = $formFactory;
    }

    /**
     * @Route("/{setId}/{page}/{noLayout}", name="solr_admin.synonyms.index", requirements={"page" = "\d+"})
     * @Template("@ezdesign/solr/admin/synonyms/list.html.Twig")
     */
    public function synonymsIndexAction(Request $request, string $setId, int $page = 1, bool $noLayout = false)
    {
        $this->permissionAccess('solradmin', 'synonyms.view');

        $manageAccess   = $this->permissionManageAccess('solradmin', ['synonyms.create', 'synonyms.delete']);
        $viewParameters = [];

        if ($this->permissionResolver->hasAccess('solradmin', 'synonyms.create')) {
            $addForm = $this->formFactory->create(AddSynonymsType::class, null);
            $addForm->handleRequest($request);
            if ($addForm->isSubmitted() && $addForm->isValid()) {
                $data     = $addForm->getData();
                $term     = $data['term'] ?? '';
                $synonyms = $data['synonyms'] ?? '';
                $this->synonymsService->addMapping(
                    $setId,
                    new SynonymsMap(
                        $term,
                        array_map('trim', explode(',', $synonyms))
                    )
                );

                $this->notificationHandler->success(
                    $this->translator->transChoice(
                        'solr_admin.action.synonyms.added',
                        count($synonyms),
                        ['%synonyms%' => $synonyms, '%term%' => $term],
                        'solr_admin'
                    )
                );

                return $this->redirectToRoute('solr_admin.synonyms.index', ['setId' => $setId]);
            }
            $viewParameters['add_form'] = $addForm->createView();
        }

        $pagerfanta = new Pagerfanta(
            new SynonymsAdapter($setId, $this->synonymsService)
        );

        $pagerfanta->setMaxPerPage(self::RESULTS_PER_PAGE);
        $pagerfanta->setCurrentPage(min($page, $pagerfanta->getNbPages()));

        $viewParameters += [
            'pager'           => $pagerfanta,
            'setId'           => $setId,
            'noLayout'        => $noLayout,
            'manageAccess'    => $manageAccess,
        ];

        return $viewParameters;
    }

    /**
     * @Route("/{setId}/add", name="solr_admin.synonyms.add")
     */
    public function addSynonymAction(string $setId, Request $request)
    {
        $this->permissionAccess('solradmin', 'synonyms.create');

        $term     = $request->request->get('term');
        $synonyms = $request->request->get('synonyms');

        $this->synonymsService->addMapping(
            $setId,
            new SynonymsMap(
                $term,
                explode(',', $synonyms)
            )
        );

        return $this->redirectToRoute(
            'solr_admin.synonyms.index',
            [
                'setId'    => $setId,
                'page'     => 1,
                'noLayout' => true,
            ]
        );
    }

    /**
     * @Route("/{setId}/terms/delete", name="solr_admin.synonyms.delete")
     */
    public function deleteSynonymAction(string $setId, Request $request)
    {
        $this->permissionAccess('solradmin', 'synonyms.delete');

        $terms = $request->request->get('termsToDelete');

        foreach ($terms as $elt) {
            $this->synonymsService->deleteMapping($setId, $elt);
        }

        return $this->redirectToRoute(
            'solr_admin.synonyms.index',
            [
                'setId'    => $setId,
                'page'     => 1,
                'noLayout' => true,
            ]
        );
    }
}
