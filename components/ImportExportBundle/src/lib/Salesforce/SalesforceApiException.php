<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Salesforce;

class SalesforceApiException extends \Exception
{
    /**
     * @param array<string, mixed> $extendedErrorDetails
     */
    public function __construct(
        protected string $errorCode,
        protected array $extendedErrorDetails = [],
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * @return array<string, mixed>
     */
    public function getExtendedErrorDetails(): array
    {
        return $this->extendedErrorDetails;
    }
}
