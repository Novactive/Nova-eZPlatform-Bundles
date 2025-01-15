<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExportBundle\Controller\Admin;

use AlmaviaCX\Bundle\IbexaImportExport\Event\PostJobCreateFormSubmitEvent;
use AlmaviaCX\Bundle\IbexaImportExport\Execution\Execution;
use AlmaviaCX\Bundle\IbexaImportExport\Job\Form\JobCreateFlow;
use AlmaviaCX\Bundle\IbexaImportExport\Job\Job;
use AlmaviaCX\Bundle\IbexaImportExport\Job\JobService;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowRegistry;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\Core\MVC\Symfony\Security\Authorization\Attribute;
use JMS\TranslationBundle\Annotation\Ignore;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Monolog\Logger;
use Pagerfanta\Adapter\CallbackAdapter;
use Pagerfanta\Doctrine\Collections\CollectionAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SuppressWarnings("PHPMD.TooManyPublicMethods")
 */
class JobController extends Controller implements TranslationContainerInterface
{
    public function __construct(
        protected FormFactoryInterface $formFactory,
        protected TranslatableNotificationHandlerInterface $notificationHandler,
        protected JobService $jobService,
        protected JobCreateFlow $jobCreateFlow,
        protected PermissionResolver $permissionResolver,
        protected EventDispatcherInterface $eventDispatcher,
        protected WorkflowRegistry $workflowRegistry,
    ) {
    }

    public function list(Request $request): Response
    {
        if (!$this->permissionResolver->hasAccess('import_export', 'workflow.list')) {
            throw new UnauthorizedException('import_export', 'workflow.list', []);
        }

        $page = $request->query->get('page') ?? 1;

        $pagerfanta = new Pagerfanta(
            new CallbackAdapter(
                function (): int {
                    return $this->jobService->countJobs();
                },
                function (int $offset, int $length): array {
                    return $this->jobService->loadJobs($length, $offset);
                }
            )
        );

        $pagerfanta->setMaxPerPage(10);
        $pagerfanta->setCurrentPage(min($page, $pagerfanta->getNbPages()));

        return $this->render('@ibexadesign/import_export/job/list.html.twig', [
            'pager' => $pagerfanta,
            'can_create' => $this->isGranted(new Attribute('import_export', 'job.create')),
        ]);
    }

    public function create(Request $request): Response
    {
        if (!$this->permissionResolver->hasAccess('import_export', 'workflow.create')) {
            throw new UnauthorizedException('import_export', 'workflow.create', []);
        }

        $job = new Job();
        $this->jobCreateFlow->bind($job);

        $form = $this->jobCreateFlow->createForm();
        if ($this->jobCreateFlow->isValid($form)) {
            $this->jobCreateFlow->saveCurrentStepData($form);
            if ($this->jobCreateFlow->nextStep()) {
                // form for the next step
                $form = $this->jobCreateFlow->createForm();
            } else {
                $this->jobCreateFlow->reset();
                try {
                    $this->eventDispatcher->dispatch(new PostJobCreateFormSubmitEvent($job));

                    $job->setCreatorId($this->permissionResolver->getCurrentUserReference()->getUserId());
                    $this->jobService->createJob($job);
                    $this->notificationHandler->success(
                        'job.create.success',
                        ['%label%' => $job->getLabel()],
                        'import_export'
                    );

                    return new RedirectResponse($this->generateUrl('import_export.job.view', [
                        'id' => $job->getId(),
                    ]));
                } catch (\Exception $exception) {
                    $this->notificationHandler->error(
                        /* @Ignore */
                        $exception->getMessage()
                    );
                }
            }
        }

        return $this->render('@ibexadesign/import_export/job/create.html.twig', [
            'form_job_create' => $form->createView(),
            'form_job_create_flow' => $this->jobCreateFlow,
        ]);
    }

    #[Entity('execution', options: ['id' => 'executionId'])]
    public function view(Request $request, Job $job, ?Execution $execution = null): Response
    {
        if (!$this->permissionResolver->hasAccess('import_export', 'job.view')) {
            throw new UnauthorizedException('import_export', 'job.view', []);
        }

        if (!$execution) {
            $execution = $job->getLastExecution();
        }

        $workflow = $this->workflowRegistry->getWorkflow($job->getWorkflowIdentifier());

        $criteria = new Criteria();
        $criteria->orderBy(['id' => 'DESC']);
        $executions = $job->getExecutions();

        $page = $request->query->get('page') ?? 1;
        $pagerfanta = new Pagerfanta(
            new CollectionAdapter($executions instanceof Selectable ? $executions->matching($criteria) : $executions)
        );

        $pagerfanta->setMaxPerPage(5);
        $pagerfanta->setCurrentPage(min($page, $pagerfanta->getNbPages()));

        return $this->render('@ibexadesign/import_export/job/view.html.twig', [
            'job' => $job,
            'workflow' => $workflow,
            'current_execution' => $execution,
            'pager' => $pagerfanta,
        ]);
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

    public function run(Job $job, ?int $batchLimit = null, bool $reset = false): RedirectResponse
    {
        $this->jobService->runJob($job, $batchLimit, $reset);

        return new RedirectResponse($this->generateUrl('import_export.job.view', [
            'id' => $job->getId(),
        ]));
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

    public function delete(Job $job): RedirectResponse
    {
        $this->jobService->delete($job);

        return new RedirectResponse($this->generateUrl('import_export.job.list'));
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message('job.create.success', 'import_export'))->setDesc("Job '%label%' created."),
        ];
    }
}
