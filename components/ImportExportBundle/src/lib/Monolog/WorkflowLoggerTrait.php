<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Monolog;

use Throwable;

trait WorkflowLoggerTrait
{
    protected $itemIndex = null;

    public function setItemIndex($itemIndex): void
    {
        $this->itemIndex = $itemIndex;
    }

    public function logException(Throwable $e): void
    {
        $this->error($e->getMessage(), ['exception' => $e->getTraceAsString()]);
    }
}
