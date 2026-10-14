<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Rest\InputParser;

use AlmaviaCX\Bundle\IbexaImportExport\Execution\ExecutionOptions;
use AlmaviaCX\Bundle\IbexaImportExport\Job\JobService;
use AlmaviaCX\Bundle\IbexaImportExport\Rest\Values\ExecuteJobRequest;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowRegistry;
use Ibexa\Contracts\Rest\Exceptions;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

class ExecuteJobInputParser extends BaseParser
{
    public function __construct(
        protected JobService $jobService,
        protected WorkflowRegistry $workflowRegistry,
    ) {
    }

    public function parse(array $data, ParsingDispatcher $parsingDispatcher): ExecuteJobRequest
    {
        if (!isset($data['JobId']) || !is_string($data['JobId'])) {
            throw new Exceptions\Parser("Missing or invalid 'JobId'.");
        }

        $job = $this->jobService->loadJobByUlid($data['JobId']);

        $workflow = $this->workflowRegistry->getWorkflow($job->getWorkflowIdentifier());
        $baseConfiguration = $workflow->getDefaultConfig();
        $processConfiguration = $baseConfiguration->getProcessConfiguration();

        $readerOptions = null;
        if (isset($data['Options'])) {
            if (!is_array($data['Options'])) {
                throw new Exceptions\Parser('Reader options must be an array.');
            }
            $requiredOptionsType = call_user_func(
                [
                    $processConfiguration->getReader()->getType(),
                    'getOptionsType',
                ]
            );

            $readerOptions = new $requiredOptionsType($data['Options']);
        }

        $executionOptions = new ExecutionOptions(
            $readerOptions
        );

        return new ExecuteJobRequest(
            $job,
            $executionOptions,
            $data[''] ?? null
        );
    }
}
