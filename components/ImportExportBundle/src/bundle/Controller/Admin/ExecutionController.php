<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExportBundle\Controller\Admin;

use AlmaviaCX\Bundle\IbexaImportExport\Execution\Execution;
use AlmaviaCX\Bundle\IbexaImportExport\Execution\ExecutionRepository;
use AlmaviaCX\Bundle\IbexaImportExport\Job\JobService;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Monolog\Logger;
use Pagerfanta\Doctrine\Collections\CollectionAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class ExecutionController extends Controller
{
    public function __construct(
        protected FormFactoryInterface $formFactory,
        protected JobService $jobService,
        protected ExecutionRepository $executionRepository
    ) {
    }

    public function delete(Execution $execution): RedirectResponse
    {
        $job = $execution->getJob();
        $this->executionRepository->delete($execution);

        return new RedirectResponse($this->generateUrl('import_export.job.view', [
           'id' => $job->getId(),
        ]));
    }

    public function retryExecution(Request $request, Execution $execution, ?int $batchLimit = null): RedirectResponse
    {
        $newExecution = $this->jobService->retryExecution($execution, $batchLimit);

        return new RedirectResponse($this->generateUrl('import_export.job.execution.view', array_merge(
            [
                'id' => $newExecution->getJob()->getId(),
                'executionId' => $newExecution->getId(),
            ],
            $request->query->all(),
        )));
    }

    public function runExecution(Request $request, Execution $execution, ?int $batchLimit = null): RedirectResponse
    {
        $this->jobService->runExecution($execution, $batchLimit);

        return new RedirectResponse($this->generateUrl('import_export.job.execution.view', array_merge(
            [
                'id' => $execution->getJob()->getId(),
                'executionId' => $execution->getId(),
            ],
            $request->query->all(),
        )));
    }

    public function pauseExecution(Request $request, Execution $execution): Response
    {
        $this->jobService->pauseJobExecution($execution);

        return new RedirectResponse($this->generateUrl('import_export.job.execution.view', array_merge(
            [
                'id' => $execution->getJob()->getId(),
                'executionId' => $execution->getId(),
            ],
            $request->query->all(),
        )));
    }

    public function cancelExecution(Request $request, Execution $execution): RedirectResponse
    {
        $this->jobService->cancelJobExecution($execution);

        return new RedirectResponse($this->generateUrl('import_export.job.execution.view', array_merge(
            [
                'id' => $execution->getJob()->getId(),
                'executionId' => $execution->getId(),
            ],
            $request->query->all(),
        )));
    }

    public function debugExecution(Execution $execution, int $index): RedirectResponse
    {
        $this->jobService->debugJobExecution($execution, $index);

        return new RedirectResponse($this->generateUrl('import_export.job.execution.view', [
            'id' => $execution->getId(),
            'executionId' => $execution->getId(),
        ]));
    }

    public function displayLogs(Execution $execution, RequestStack $requestStack): Response
    {
        $request = $requestStack->getMainRequest();

        $countsByLevel = $this->jobService->getJobExecutionLogsCountByLevel($execution);

        $countsByLevel = [null => array_sum($countsByLevel)] + $countsByLevel;
        $choices = [];
        foreach ($countsByLevel as $level => $count) {
            $choices[$level] = sprintf(
                '%s (%d)',
                !empty($level) ? Logger::getLevelName((int) $level) : 'ALL',
                $count
            );
        }
        $formBuilder = $this->formFactory->createNamedBuilder('logs', FormType::class, null, ['method' => 'GET']);
        $formBuilder->add('level', ChoiceType::class, [
            'label' => 'job.logs.level',
            'choices' => array_flip($choices),
            'attr' => [
                'class' => 'ibexa-form-autosubmit',
            ],
        ]);
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        $logsQuery = $request->get('logs', []) + ['page' => 1, 'level' => null];

        $logs = $this->jobService->getJobExecutionLogs(
            $execution,
            $logsQuery['level'] ? (int) $logsQuery['level'] : null
        );
        $pager = new Pagerfanta(new CollectionAdapter($logs));
        $pager->setMaxPerPage(50);
        $pager->setCurrentPage($logsQuery['page']);

        return $this->render('@ibexadesign/import_export/job/logs.html.twig', [
            'execution' => $execution,
            'logs' => $pager,
            'form' => $form->createView(),
            'request_query' => $request->query->all(),
        ]);
    }
}
