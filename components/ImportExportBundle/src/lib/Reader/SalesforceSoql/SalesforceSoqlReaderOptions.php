<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\SalesforceSoql;

use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderOptions;
use AlmaviaCX\Bundle\IbexaImportExport\Salesforce\SalesforceApiCredentials;

/**
 * @property string                   $queryString
 * @property string                   $countQueryString
 * @property string                   $domain
 * @property string                   $version
 * @property SalesforceApiCredentials $credentials
 * @property array                    $queryParameters
 */
class SalesforceSoqlReaderOptions extends ReaderOptions
{
    protected string $queryString;
    protected string $countQueryString;
    protected string $domain;
    protected string $version;
    protected SalesforceApiCredentials $credentials;
    protected array $queryParameters = [];
}
