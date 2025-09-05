<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Monolog;

trait WorkflowLoggerTrait
{
    protected $itemIndex;

    public function setItemIndex($itemIndex): void
    {
        $this->itemIndex = $itemIndex;
    }

    public function logException(\Throwable $e): void
    {
        $this->error($e->getMessage(), ['exception' => $e->getTraceAsString()]);
    }
}
