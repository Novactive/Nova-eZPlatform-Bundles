<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Rest\ValueObjectVisitor;

use AlmaviaCX\Bundle\IbexaImportExport\Execution\Execution;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

class ExecutionVisitor extends ValueObjectVisitor
{
    /**
     * @param Execution $data
     *
     * @return void
     */
    public function visit(Visitor $visitor, Generator $generator, $data)
    {
        $visitor->setHeader('Content-Type', $generator->getMediaType('Execution'));
        $generator->startObjectElement('Execution');
        $generator->valueElement('JobId', $data->getJob()->getUlid());
        $generator->valueElement('Id', $data->getId());
        $generator->startObjectElement('WorkflowState');
        $generator->valueElement('StartTime', $data->getWorkflowState()->getStartTime());
        $generator->valueElement('EndTime', $data->getWorkflowState()->getEndTime());
        $generator->valueElement('TotalItemsCount', $data->getWorkflowState()->getTotalItemsCount());
        $generator->valueElement('Offset', $data->getWorkflowState()->getOffset());
        $generator->endObjectElement('WorkflowState');
        $generator->endObjectElement('Execution');
    }
}
