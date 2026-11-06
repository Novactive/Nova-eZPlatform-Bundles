<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExportBundle\Controller\Rest;

use AlmaviaCX\Bundle\IbexaImportExport\Execution\Execution;
use AlmaviaCX\Bundle\IbexaImportExport\Job\JobService;
use AlmaviaCX\Bundle\IbexaImportExport\Rest\Values\ExecuteJobRequest;
use Ibexa\Rest\Message;
use Ibexa\Rest\Server\Controller;
use Symfony\Component\HttpFoundation\Request;

class JobController extends Controller
{
    public function __construct(
        protected JobService $jobService,
    ) {
    }

    public function executeJob(Request $request): Execution
    {
        /** @var ExecuteJobRequest $executeJobRequest */
        $executeJobRequest = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        return $this->jobService->executeJob($executeJobRequest->job, $executeJobRequest->executionOptions);
    }
}
