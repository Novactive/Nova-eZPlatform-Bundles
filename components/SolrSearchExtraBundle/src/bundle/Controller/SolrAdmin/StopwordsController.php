<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtraBundle\Controller\SolrAdmin;

use Novactive\EzSolrSearchExtra\Api\Schema\Analysis\Stopwords\StopwordsService;
use Novactive\EzSolrSearchExtra\Form\AddStopWordType;
use Novactive\EzSolrSearchExtra\Pagination\Pagerfanta\StopwordsAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
    protected const int RESULTS_PER_PAGE = 20;
    
    /**
     * StopwordsController constructor.
     */
    public function __construct(protected StopwordsService $stopwordsService, protected FormFactoryInterface $formFactory)
    {
    }

    /**
     * @param $words
     */
    public function getWordsData($words): array
    {
        $ids = [];
        foreach ($words as $word) {
            $ids[] = $word;
        }

        return $ids;
    }

    /**
     * @Route("/{setId}/{page}/{noLayout}", name="solr_admin.stopwords.index", requirements={"page" = "\d+"})
     * @Template("@ibexadesign/solr/admin/stopwords/list.html.Twig")
     *
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function stopwordsIndexAction(Request $request, string $setId, int $page = 1, bool $noLayout = false)
    {
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
                    array_map('trim', explode(',', $words))
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

        return $viewParameters;
    }

    /**
     * @Route("/{setId}/add", name="solr_admin.stopwords.add")
     *
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function addStopwordAction(string $setId, Request $request): RedirectResponse
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
                'setId' => $setId,
                'page' => 1,
                'noLayout' => true,
            ]
        );
    }

    /**
     * @Route("/{setId}/word/delete", name="solr_admin.stopwords.delete")
     *
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function deleteStopwordsAction(string $setId, Request $request): RedirectResponse
    {
        $this->permissionAccess('solradmin', 'stopwords.delete');

        $words = $request->request->get('wordsToDelete');
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
