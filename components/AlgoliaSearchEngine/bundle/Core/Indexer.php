<?php

/**
 * Nova eZ Algolia Search Engine.
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @licence   "SEE FULL LICENSE OPTIONS IN LICENSE.md"
 *            Nova eZ Algolia Search Engine is tri-licensed, meaning you must choose one of three licenses to use:
 *                - Commercial License: a paid license, meant for commercial use. The default option for most users.
 *                - Creative Commons Non-Commercial No-Derivatives: meant for trial and non-commercial use.
 *                - GPLv3 License: meant for open-source projects.
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZAlgoliaSearchEngine\Core;

use Doctrine\DBAL\Connection;
use Exception;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\Core\Search\Common\IncrementalIndexer;
use eZ\Publish\SPI\Persistence\Content\ContentInfo;
use eZ\Publish\SPI\Persistence\Handler as PersistenceHandler;
use eZ\Publish\SPI\Search\Handler as SearchHandler;
use Novactive\Bundle\eZAlgoliaSearchEngine\Mapping\ParametersResolver;
use Psr\Log\LoggerInterface;

class Indexer extends IncrementalIndexer
{
    /**
     * @var Handler
     */
    private $handler;

    /**
     * @var Converter;
     */
    private $converter;

    /**
     * @var DocumentSerializer
     */
    private $documentSerializer;

    /**
     * @var LanguageService
     */
    private $languageService;

    /**
     * @var ParametersResolver
     */
    private $parametersResolver;

    public function __construct(
        LoggerInterface $logger,
        PersistenceHandler $persistenceHandler,
        Connection $connection,
        SearchHandler $searchHandler,
        Handler $handler,
        Converter $converter,
        DocumentSerializer $documentSerializer,
        LanguageService $languageService,
        ParametersResolver $parametersResolver
    ) {
        parent::__construct($logger, $persistenceHandler, $connection, $searchHandler);
        $this->handler = $handler;
        $this->converter = $converter;
        $this->documentSerializer = $documentSerializer;
        $this->languageService = $languageService;
        $this->parametersResolver = $parametersResolver;
    }

    public function getName(): string
    {
        return 'eZ Platform Algolia Search Engine';
    }

    public function purge(): void
    {
        $this->handler->purgeIndex();
    }

    public function updateSearchIndex(array $contentIds, $commit): void
    {
        $contentHandler = $this->persistenceHandler->contentHandler();
        $locationHandler = $this->persistenceHandler->locationHandler();

        $langObjectSet = [];
        foreach ($contentIds as $contentId) {
            try {
                $contentInfo = $contentHandler->loadContentInfo($contentId);
                $contentType = $this->persistenceHandler->contentTypeHandler()->load($contentInfo->contentTypeId);
                if (!$this->parametersResolver->ifContentTypeAllowed($contentType->identifier)) {
                    continue;
                }

                if (ContentInfo::STATUS_PUBLISHED === $contentInfo->status) {
                    $content = $contentHandler->load($contentId);
                    $this->convertDocuments($this->converter->convertContent($content), $langObjectSet);

                    foreach ($locationHandler->loadLocationsByContent($contentId) as $location) {
                        $this->convertDocuments($this->converter->convertLocation($location), $langObjectSet);
                    }
                } else {
                    $this->handler->deleteContent($contentId);
                }
            } catch (Exception $e) {
                $this->handler->deleteContent($contentId);
                $this->logger->error(
                    'Unable to index the content',
                    [
                        'contentId' => $contentId,
                        'error' => $e->getMessage(),
                    ]
                );
            }
        }
        foreach ($langObjectSet as $languageCode => $objects) {
            $this->handler->reindex($languageCode, $objects);
        }
    }

    private function convertDocuments(iterable $documents, &$langObjectSet): void
    {
        $contentLanguages = $mainTranslation = [];
        foreach ($documents as $document) {
            $serialized = $this->documentSerializer->serialize($document);
            $serialized['objectID'] = $document->id;
            $langObjectSet[$serialized['meta_indexed_language_code_s']][] = $serialized;
            $contentLanguages[] = $serialized['meta_indexed_language_code_s'];
            if ($document->isMainTranslation) {
                $mainTranslation = $serialized;
            }
        }

        foreach ($this->languageService->loadLanguages() as $language) {
            if (!\in_array($language->languageCode, $contentLanguages, true)) {
                $langObjectSet[$language->languageCode][] = $mainTranslation;
            }
        }
    }
}
