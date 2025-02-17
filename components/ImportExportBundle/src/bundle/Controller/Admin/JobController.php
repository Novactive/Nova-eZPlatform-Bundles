<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExportBundle\Controller\Admin;

use AlmaviaCX\Bundle\IbexaImportExport\Event\PostJobCreateFormSubmitEvent;
use AlmaviaCX\Bundle\IbexaImportExport\Job\Form\JobCreateFlow;
use AlmaviaCX\Bundle\IbexaImportExport\Job\Job;
use AlmaviaCX\Bundle\IbexaImportExport\Job\JobService;
use Exception;
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
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\VarExporter\Instantiator;

class JobController extends Controller implements TranslationContainerInterface
{
    protected FormFactoryInterface $formFactory;
    protected TranslatableNotificationHandlerInterface $notificationHandler;
    protected JobService $jobService;
    protected JobCreateFlow $jobCreateFlow;
    protected PermissionResolver $permissionResolver;
    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(
        FormFactoryInterface $formFactory,
        TranslatableNotificationHandlerInterface $notificationHandler,
        JobService $jobService,
        JobCreateFlow $jobCreateFlow,
        PermissionResolver $permissionResolver,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->formFactory = $formFactory;
        $this->notificationHandler = $notificationHandler;
        $this->jobService = $jobService;
        $this->jobCreateFlow = $jobCreateFlow;
        $this->permissionResolver = $permissionResolver;
        $this->eventDispatcher = $eventDispatcher;
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

        $job = Instantiator::instantiate(Job::class);
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
                } catch (Exception $exception) {
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

    public function view(Job $job): Response
    {
        if (!$this->permissionResolver->hasAccess('import_export', 'job.views')) {
            throw new UnauthorizedException('import_export', 'job.views', []);
        }

        return $this->render('@ibexadesign/import_export/job/view.html.twig', [
            'job' => $job,
        ]);
    }

    public function displayLogs(Job $job, RequestStack $requestStack): Response
    {
        $request = $requestStack->getMainRequest();

        $countsByLevel = $this->jobService->getJobLogsCountByLevel($job);
        $formBuilder = $this->formFactory->createNamedBuilder('logs', FormType::class, null, ['method' => 'GET']);
        $formBuilder->add('level', ChoiceType::class, [
            'label' => 'job.logs.level',
            'choices' => array_flip([null => array_sum($countsByLevel)] + $countsByLevel),
            'choice_label' => function ($choice, int $count, $level) {
                return sprintf(
                    '%s (%d)',
                    $level ? Logger::getLevelName((int) $level) : 'ALL',
                    $count
                );
            },
            'attr' => [
                'class' => 'ibexa-form-autosubmit',
            ],
        ]);
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        $logsQuery = $request->get('logs', []) + ['page' => 1, 'level' => null];

        $logs = $this->jobService->getJobLogs($job, $logsQuery['level'] ? (int) $logsQuery['level'] : null);
        $pager = new Pagerfanta(new CollectionAdapter($logs));
        $pager->setMaxPerPage(50);
        $pager->setCurrentPage($logsQuery['page']);

        return $this->render('@ibexadesign/import_export/job/logs.html.twig', [
            'job' => $job,
            'logs' => $pager,
            'form' => $form->createView(),
            'request_query' => $request->query->all(),
        ]);
    }

    public function run(Job $job, int $batchLimit = null, bool $reset = false): Response
    {
        $this->jobService->runJob($job, $batchLimit, $reset);

        return new RedirectResponse($this->generateUrl('import_export.job.view', [
            'id' => $job->getId(),
        ]));
    }

    public function cancel(Job $job): Response
    {
        $this->jobService->cancelJob($job);

        return new RedirectResponse($this->generateUrl('import_export.job.view', [
            'id' => $job->getId(),
        ]));
    }

    public function debug(Job $job, int $index)
    {
        $this->jobService->debug($job, $index);

        return new RedirectResponse($this->generateUrl('import_export.job.view', [
            'id' => $job->getId(),
        ]));
    }

    public function delete(Job $job): Response
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
