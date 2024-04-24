<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Monolog;

use Psr\Log\LoggerInterface;

interface WorkflowLoggerInterface extends LoggerInterface
{
    public function setItemIndex($itemIndex): void;
}
