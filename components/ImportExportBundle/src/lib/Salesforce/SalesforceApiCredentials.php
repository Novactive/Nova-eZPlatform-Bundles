<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Salesforce;

class SalesforceApiCredentials
{
    public function __construct(
        public readonly string $clientId,
        public readonly string $clientSecret,
        public readonly string $username,
        public readonly string $password
    ) {
    }

    public function __toString(): string
    {
        return md5(
            $this->clientId.
            $this->clientSecret.
            $this->username.
            $this->password
        );
    }
}
