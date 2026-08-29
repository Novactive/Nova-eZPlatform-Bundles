<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExportBundle\Controller\Admin;

use AlmaviaCX\Bundle\IbexaImportExport\Execution\Execution;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\RunConfigurationBuilder;
use AlmaviaCX\Bundle\IbexaImportExport\Workflow\WorkflowRegistry;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ReaderController extends Controller
{
    public function __construct(
        protected WorkflowRegistry $workflowRegistry,
        protected RunConfigurationBuilder $runConfigurationBuilder,
    ) {
    }

    public function displayDetails(Execution $execution): Response
    {
        $workflow = $this->workflowRegistry->getWorkflow($execution->getWorkflowIdentifier());
        $runConfiguration = ($this->runConfigurationBuilder)(
            $workflow,
            $execution->getOptions()
        );

        $reader = $runConfiguration->getReader();
        $detailsTemplate = $reader::getDetailsTemplate();
        if (!$detailsTemplate) {
            return new Response('');
        }

        return $this->render($detailsTemplate, [
            'reader' => $reader,
        ]);
    }
}
