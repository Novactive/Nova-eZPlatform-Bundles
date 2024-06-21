<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExportBundle\Controller\Admin;

use AlmaviaCX\Bundle\IbexaImportExport\Component\ComponentRegistry;
use AlmaviaCX\Bundle\IbexaImportExport\File\DownloadFileResponse;
use AlmaviaCX\Bundle\IbexaImportExport\File\FileHandler;
use AlmaviaCX\Bundle\IbexaImportExport\Job\Job;
use AlmaviaCX\Bundle\IbexaImportExport\Writer\Stream\AbstractStreamWriter;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;
use Twig\Error\LoaderError;

class WriterController extends Controller
{
    protected Environment $twig;
    protected ComponentRegistry $componentRegistry;
    protected FileHandler $fileHandler;

    public function __construct(Environment $twig, ComponentRegistry $componentRegistry, FileHandler $fileHandler)
    {
        $this->twig = $twig;
        $this->componentRegistry = $componentRegistry;
        $this->fileHandler = $fileHandler;
    }

    public function displayResults(Job $job): Response
    {
        $results = [];
        foreach ($job->getWriterResults() as $index => $writerResults) {
            try {
                /** @var \AlmaviaCX\Bundle\IbexaImportExport\Writer\WriterInterface $writer */
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
                        'job' => $job,
                    ],
                ];
            } catch (LoaderError|NotFoundException $e) {
                throw $e;
            }
        }

        return $this->render('@ibexadesign/import_export/job/results.html.twig', [
            'job' => $job,
            'results' => $results,
        ]);
    }

    /**
     * @param int|string $writerIndex
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function downloadFile(Job $job, $writerIndex): DownloadFileResponse
    {
        $writerResults = $job->getWriterResults()[$writerIndex];
        $writer = $this->componentRegistry->getComponent($writerResults->getWriterType());
        if (!$writer instanceof AbstractStreamWriter) {
            throw new NotFoundHttpException();
        }

        $response = new DownloadFileResponse($writerResults->getResults()['filepath'], $this->fileHandler);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $job->getLabel(),
        );

        return $response;
    }
}
