<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Reader\SalesforceSoql;

use AlmaviaCX\Bundle\IbexaImportExport\Reader\AbstractReader;
use AlmaviaCX\Bundle\IbexaImportExport\Reader\ReaderIteratorInterface;
use AlmaviaCX\Bundle\IbexaImportExport\Salesforce\SalesforceApiClient;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * @extends AbstractReader<SalesforceSoqlReaderOptions>
 */
class SalesforceSoqlReader extends AbstractReader implements TranslationContainerInterface
{
    public function __construct(
        protected SalesforceApiClient $apiClient
    ) {
    }

    /**
     * @return SalesforceSoqlIterator
     */
    public function __invoke(): ReaderIteratorInterface
    {
        $options = $this->getOptions();

        $queryString = $options->queryString;
        $countQueryString = $options->countQueryString;
        $queryParameters = $options->queryParameters;

        $queryString = strtr($queryString, $queryParameters);
        $countQueryString = strtr($countQueryString, $queryParameters);

        return new SalesforceSoqlIterator(
            $this->apiClient,
            $options->credentials,
            $options->domain,
            $options->version,
            $queryString,
            $countQueryString,
            $this->getCache()
        );
    }

    /**
     * @return ArrayCollection<string, mixed>
     */
    protected function getCache(): ArrayCollection
    {
        return $this->workflowState->getCacheItem('reader_cache', new ArrayCollection());
    }

    public static function getName(): TranslatableMessage
    {
        return new TranslatableMessage('reader.salesforce.soql.name', [], 'import_export');
    }

    public static function getTranslationMessages(): array
    {
        return [( new Message('reader.salesforce.soql.name', 'import_export') )->setDesc('Salesforce SOQL')];
    }

    public static function getOptionsType(): string
    {
        return SalesforceSoqlReaderOptions::class;
    }

    public static function getOptionsFormType(): ?string
    {
        return SalesforceSoqlReaderOptionsFormType::class;
    }
}
