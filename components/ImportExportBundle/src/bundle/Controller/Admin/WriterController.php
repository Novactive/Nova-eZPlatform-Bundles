<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExportBundle\Controller\Admin;

use AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentRegistry;
use AlmaviaCX\Bundle\IbexaImportExport\Execution\Execution;
use AlmaviaCX\Bundle\IbexaImportExport\File\DownloadFileResponse;
use AlmaviaCX\Bundle\IbexaImportExport\File\FileHandler;
use AlmaviaCX\Bundle\IbexaImportExport\Writer\Stream\AbstractStreamWriter;
use AlmaviaCX\Bundle\IbexaImportExport\Writer\WriterInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Writer\WriterOptions;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;
use Twig\Error\LoaderError;

class WriterController extends Controller
{
    public function __construct(
        protected Environment $twig,
        protected ComponentRegistry $componentRegistry,
        protected FileHandler $fileHandler
    ) {
    }

    /**
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function displayResults(Execution $execution): Response
    {
        $results = [];
        foreach ($execution->getWorkflowState()->getWritersResults() as $index => $writerResults) {
            try {
                /** @var \AlmaviaCX\Bundle\IbexaImportExport\Writer\WriterInterface<WriterOptions> $writer */
                $writer = $this->componentRegistry->getComponent($writerResults->getWriterType());
                $template = $writer::getResultTemplate();
                if (!$template) {
                    continue;
                }
                $this->twig->load($template);

                $results[] = [
                    'template' => $template,
                    'parameters' => [
                        'results' => $writerResults->getResults(),
                        'writerIndex' => $index,
                        'writer' => $writer,
                        'execution' => $execution,
                    ],
                ];
            } catch (LoaderError|NotFoundException $e) {
                throw $e;
            }
        }

        return $this->render('@ibexadesign/import_export/job/results.html.twig', [
            'execution' => $execution,
            'results' => $results,
        ]);
    }

    public function downloadFile(Execution $execution, string $writerId): DownloadFileResponse
    {
        $writerResults = $execution->getWorkflowState()->getWriterResults($writerId);
        $writer = $this->componentRegistry->getComponent($writerResults->getWriterType());
        if (!$writer instanceof AbstractStreamWriter) {
            throw new NotFoundHttpException();
        }

        $response = new DownloadFileResponse($writerResults->getResults()['filepath'], $this->fileHandler);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $execution->getJob()->getLabel(),
        );

        return $response;
    }

    /**
     * @param array<string, mixed>                                                        $results
     * @param \AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Content\IbexaContentWriter $writer
     */
    public function displayIbexaContentWriterResults(
        array $results,
        string $writerIndex,
        WriterInterface $writer,
        Execution $execution,
        RequestStack $requestStack
    ): Response {
        $request = $requestStack->getMainRequest();
        $query = $request->get($writerIndex, []) + ['page' => 1];

        $pager = new Pagerfanta(new ArrayAdapter($results['imported_content_ids'] ?? []));
        $pager->setMaxPerPage(25);
        $pager->setCurrentPage($query['page']);

        return $this->render('@ibexadesign/import_export/writer/results/ibexa/content.html.twig', [
            'results' => $pager,
            'writer' => $writer,
            'writerIndex' => $writerIndex,
            'execution' => $execution,
            'request_query' => $request->query->all(),
        ]);
    }
}
