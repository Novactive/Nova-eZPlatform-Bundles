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

use Novactive\EzSolrSearchExtra\Api\Schema\Analysis\Stopwords\StopwordsService;
use Novactive\EzSolrSearchExtra\Form\AddStopWordType;
use Novactive\EzSolrSearchExtra\Pagination\Pagerfanta\StopwordsAdapter;
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
 * @Route("/solr/admin/stopwords")
 */
class StopwordsController extends BaseController
{
    protected const RESULTS_PER_PAGE = 20;

    /** @var StopwordsService */
    protected $stopwordsService;

    /** @var FormFactory */
    protected $formFactory;

    /**
     * StopwordsController constructor.
     */
    public function __construct(StopwordsService $stopwordsService, FormFactory $formFactory)
    {
        $this->stopwordsService = $stopwordsService;
        $this->formFactory      = $formFactory;
    }

    public function getWordsData($words)
    {
        $ids = [];
        foreach ($words as $word) {
            $ids[] = $word;
        }

        return $ids;
    }

    /**
     * @Route("/{setId}/{page}/{noLayout}", name="solr_admin.stopwords.index", requirements={"page" = "\d+"})
     * @Template("@ezdesign/solr/admin/stopwords/list.html.Twig")
     */
    public function stopwordsIndexAction(Request $request, string $setId, int $page = 1, bool $noLayout = false)
    {
        $this->permissionAccess('solradmin', 'stopwords.view');

        $manageAccess   = $this->permissionManageAccess('solradmin', ['stopwords.delete']);
        $viewParameters = [];

        if ($this->permissionResolver->hasAccess('solradmin', 'stopwords.create')) {
            $addForm = $this->formFactory->create(AddStopWordType::class, null);
            $addForm->handleRequest($request);
            if ($addForm->isSubmitted() && $addForm->isValid()) {
                $data  = $addForm->getData();
                $words = $data['words'] ?? '';
                $this->stopwordsService->addWords(
                    $setId,
                    array_map('trim', explode(',', $words))
                );

                $this->notificationHandler->success(
                    $this->translator->transChoice(
                        'solr_admin.action.stopwords.added',
                        count($words),
                        ['%words%' => $words],
                        'solr_admin'
                    )
                );

                return $this->redirectToRoute('solr_admin.stopwords.index', ['setId' => $setId]);
            }
            $viewParameters['add_form'] = $addForm->createView();
        }

        $pagerfanta = new Pagerfanta(
            new StopwordsAdapter($setId, $this->stopwordsService)
        );

        $pagerfanta->setMaxPerPage(self::RESULTS_PER_PAGE);
        $pagerfanta->setCurrentPage(min($page, $pagerfanta->getNbPages()));

        $viewParameters += [
            'pager'        => $pagerfanta,
            'setId'        => $setId,
            'noLayout'     => $noLayout,
            'manageAccess' => $manageAccess,
        ];

        return $viewParameters;
    }

    /**
     * @Route("/{setId}/add", name="solr_admin.stopwords.add")
     */
    public function addStopwordAction(string $setId, Request $request)
    {
        $this->permissionAccess('solradmin', 'stopwords.create');

        $words = $request->request->get('words');

        $words = array_map('trim', explode(',', $words));
        $this->stopwordsService->addWords(
            $setId,
            $words
        );

        return $this->redirectToRoute(
            'solr_admin.stopwords.index',
            [
                'setId'    => $setId,
                'page'     => 1,
                'noLayout' => true,
            ]
        );
    }

    /**
     * @Route("/{setId}/word/delete", name="solr_admin.stopwords.delete")
     */
    public function deleteStopwordsAction(string $setId, Request $request)
    {
        $this->permissionAccess('solradmin', 'stopwords.delete');

        $words = $request->request->get('wordsToDelete');
        foreach ($words as $elt) {
            $this->stopwordsService->deleteWord($setId, $elt);
        }

        return $this->redirectToRoute(
            'solr_admin.stopwords.index',
            [
                'setId'    => $setId,
                'page'     => 1,
                'noLayout' => true,
            ]
        );
    }
}
