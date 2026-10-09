<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Monolog;

use Throwable;

trait WorkflowLoggerTrait
{
    protected ?int $itemIndex = null;

    public function setItemIndex(?int $itemIndex): void
    {
        $this->itemIndex = $itemIndex;
    }

    public function logException(Throwable $e): void
    {
        $this->error($e->getMessage(), ['exception' => $e->getTraceAsString()]);
    }
}
