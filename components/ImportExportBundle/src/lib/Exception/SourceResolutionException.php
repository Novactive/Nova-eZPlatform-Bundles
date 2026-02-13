<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Exception;

use Throwable;

class SourceResolutionException extends BaseException
{
    protected $source;

    public function __construct($source, ?Throwable $previous = null)
    {
        $this->source = $source;
        parent::__construct(sprintf('[%s] %s', $source, $previous->getMessage()), $previous->getCode(), $previous);
    }
}
