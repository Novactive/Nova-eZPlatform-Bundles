<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtraBundle\Controller\SolrAdmin;

use Novactive\EzSolrSearchExtra\Api\Schema\Analysis\Stopwords\StopwordsService;
use Novactive\EzSolrSearchExtra\Form\AddStopWordType;
use Novactive\EzSolrSearchExtra\Pagination\Pagerfanta\StopwordsAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/solr/admin/stopwords')]
class StopwordsController extends BaseController
{
    protected const int RESULTS_PER_PAGE = 20;

    public function __construct(
        protected StopwordsService $stopwordsService,
        protected FormFactoryInterface $formFactory
    ) {
    }

    #[Route('/{setId}/{page}/{noLayout}', name: 'solr_admin.stopwords.index', requirements: ['page' => '\d+'])]
    public function stopwordsIndexAction(
        Request $request,
        string $setId,
        int $page = 1,
        bool $noLayout = false
    ): Response {
        $this->permissionAccess('solradmin', 'stopwords.view');

        $manageAccess = $this->permissionManageAccess('solradmin', ['stopwords.delete']);
        $viewParameters = [];

        if ($this->permissionResolver->hasAccess('solradmin', 'stopwords.create')) {
            $addForm = $this->formFactory->create(AddStopWordType::class);
            $addForm->handleRequest($request);
            if ($addForm->isSubmitted() && $addForm->isValid()) {
                $data = $addForm->getData();
                $words = $data['words'] ?? [];
                $this->stopwordsService->addWords(
                    $setId,
                    array_map(trim(...), explode(',', $words))
                );

                $this->notificationHandler->success(
                    $this->translator->trans(
                        'solr_admin.action.stopwords.added',
                        [
                            '%words%' => $words,
                            'count' => count(explode(',', $words)),
                        ],
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
            'pager' => $pagerfanta,
            'setId' => $setId,
            'noLayout' => $noLayout,
            'manageAccess' => $manageAccess,
        ];

        return $this->render('@ibexadesign/solr/admin/stopwords/list.html.Twig', $viewParameters);
    }

    #[Route('/{setId}/add', name: 'solr_admin.stopwords.add')]
    public function addStopwordAction(string $setId, Request $request): RedirectResponse
    {
        $this->permissionAccess('solradmin', 'stopwords.create');

        $words = $request->request->get('words');

        $words = array_map(trim(...), explode(',', $words));
        $this->stopwordsService->addWords(
            $setId,
            $words
        );

        return $this->redirectToRoute(
            'solr_admin.stopwords.index',
            [
                'setId' => $setId,
                'page' => 1,
                'noLayout' => true,
            ]
        );
    }

    #[Route('/{setId}/word/delete', name: 'solr_admin.stopwords.delete')]
    public function deleteStopwordsAction(string $setId, Request $request): RedirectResponse
    {
        $this->permissionAccess('solradmin', 'stopwords.delete');

        $words = $request->get('wordsToDelete');
        foreach ($words as $elt) {
            $this->stopwordsService->deleteWord($setId, $elt);
        }

        return $this->redirectToRoute(
            'solr_admin.stopwords.index',
            [
                'setId' => $setId,
                'page' => 1,
                'noLayout' => true,
            ]
        );
    }
}
