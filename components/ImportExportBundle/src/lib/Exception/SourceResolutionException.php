<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Exception;

use Throwable;

class SourceResolutionException extends BaseException
{
    public function __construct(
        protected string $source,
        ?Throwable $previous = null
    ) {
        parent::__construct(sprintf('[%s] %s', $source, $previous->getMessage()), $previous->getCode(), $previous);
    }
}
