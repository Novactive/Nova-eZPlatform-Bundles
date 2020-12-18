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

use Algolia\AlgoliaSearch\SearchIndex;
use Exception;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Search\Legacy\Content\Handler as LegacyHandler;
use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\Location;
use Novactive\Bundle\eZAlgoliaSearchEngine\Core\DataCollector\Logger;
use Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\Search;
use Novactive\Bundle\eZAlgoliaSearchEngine\Mapping\ParametersResolver;
use Psr\Log\LoggerInterface;

class Handler extends LegacyHandler
{
    /**
     * @var AlgoliaClient
     */
    private $client;

    /**
     * @var Converter
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
     * @var DocumentIdGenerator
     */
    private $documentIdGenerator;

    /**
     * @var Search
     */
    private $contentSearchService;

    /**
     * @var Search
     */
    private $locationSearchService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ParametersResolver
     */
    private $parametersResolver;

    /**
     * @var ContentService
     */
    private $contentService;

    /**
     * @var ContentTypeService
     */
    private $contentTypeService;

    /**
     * @var Logger
     */
    private $collector;

    /**
     * @required
     */
    public function setServices(
        AlgoliaClient $client,
        Converter $converter,
        DocumentSerializer $documentSerializer,
        LanguageService $languageService,
        DocumentIdGenerator $documentIdGenerator,
        Search $contentSearch,
        Search $locationSearch,
        LoggerInterface $logger,
        ParametersResolver $parametersResolver,
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        Logger $collector
    ): void {
        $this->client = $client;
        $this->converter = $converter;
        $this->documentSerializer = $documentSerializer;
        $this->languageService = $languageService;
        $this->documentIdGenerator = $documentIdGenerator;
        $this->contentSearchService = $contentSearch;
        $this->locationSearchService = $locationSearch;
        $this->logger = $logger;
        $this->parametersResolver = $parametersResolver;
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->collector = $collector;
    }

    public function indexContent(Content $content): void
    {
        $contentType = $this->contentTypeService->loadContentType($content->versionInfo->contentInfo->contentTypeId);
        if ($this->parametersResolver->ifContentTypeAllowed($contentType->identifier)) {
            try {
                $contentLanguages = $mainTranslation = [];
                foreach ($this->converter->convertContent($content) as $document) {
                    $serialized = $this->documentSerializer->serialize($document);
                    $serialized['objectID'] = $document->id;
                    $this->reindex($serialized['meta_indexed_language_code_s'], [$serialized]);
                    $contentLanguages[] = $serialized['meta_indexed_language_code_s'];
                    if ($document->isMainTranslation) {
                        $mainTranslation = $serialized;
                    }
                }

                foreach ($this->languageService->loadLanguages() as $language) {
                    if (!\in_array($language->languageCode, $contentLanguages, true)) {
                        $this->reindex($language->languageCode, [$mainTranslation]);
                    }
                }
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }

        parent::indexContent($content);
    }

    public function indexLocation(Location $location): void
    {
        $content = $this->contentService->loadContent($location->contentId);
        if ($this->parametersResolver->ifContentTypeAllowed($content->getContentType()->identifier)) {
            try {
                $locationLanguages = $mainTranslation = [];
                foreach ($this->converter->convertLocation($location) as $document) {
                    $serialized = $this->documentSerializer->serialize($document);
                    $serialized['objectID'] = $document->id;
                    $this->reindex($serialized['meta_indexed_language_code_s'], [$serialized]);
                    $locationLanguages[] = $serialized['meta_indexed_language_code_s'];
                    if ($document->isMainTranslation) {
                        $mainTranslation = $serialized;
                    }
                }

                foreach ($this->languageService->loadLanguages() as $language) {
                    if (!\in_array($language->languageCode, $locationLanguages, true)) {
                        $this->reindex($language->languageCode, [$mainTranslation]);
                    }
                }
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }

        parent::indexLocation($location);
    }

    public function reindex(string $languageCode, array $objects): void
    {
        $mode = AlgoliaClient::CLIENT_ADMIN_MODE;
        ($this->client)(
            function (SearchIndex $index) use ($objects, $mode, $languageCode) {
                $start = microtime(true);
                $index->saveObjects($objects);
                $this->collector->addSave($mode, $languageCode, $index->getIndexName(), $objects)->startTime($start);
            },
            $languageCode,
            $mode
        );
    }

    public function deleteContent($contentId, $versionId = null): void
    {
        $mode = AlgoliaClient::CLIENT_ADMIN_MODE;
        foreach ($this->languageService->loadLanguages() as $language) {
            ($this->client)(
                function (SearchIndex $index) use ($contentId, $language, $mode) {
                    $object = $this->documentIdGenerator->generateContentDocumentId(
                        (int) $contentId,
                        $language->languageCode
                    );
                    $start = microtime(true);
                    $index->deleteObject($object);
                    $this->collector->addDelete($mode, $language->languageCode, $index->getIndexName(), [$object])
                                    ->startTime($start);
                },
                $language->languageCode,
                $mode
            );
        }

        parent::deleteContent($contentId, $versionId);
    }

    public function deleteLocation($locationId, $versionId = null): void
    {
        $mode = AlgoliaClient::CLIENT_ADMIN_MODE;
        foreach ($this->languageService->loadLanguages() as $language) {
            ($this->client)(
                function (SearchIndex $index) use ($language, $locationId, $mode) {
                    $object = $this->documentIdGenerator->generateLocationDocumentId(
                        (int) $locationId,
                        $language->languageCode
                    );
                    $start = microtime(true);
                    $index->deleteObject($object);
                    $this->collector->addDelete($mode, $language->languageCode, $index->getIndexName(), [$object])
                                    ->startTime($start);
                },
                $language->languageCode,
            );
        }

        parent::deleteLocation($locationId, $versionId);
    }

    public function purgeIndex(): void
    {
        $mode = AlgoliaClient::CLIENT_ADMIN_MODE;
        foreach ($this->languageService->loadLanguages() as $language) {
            ($this->client)(
                function (SearchIndex $index) use ($mode) {
                    $start = microtime(true);
                    $index->clearObjects();
                    $this->collector->addPurge($mode, $index->getIndexName())->startTime($start);
                },
                $language->languageCode,
                $mode
            );
        }

        parent::purgeIndex();
    }

    public function findSingle(Criterion $filter, array $languageFilter = []): ValueObject
    {
        $query = new Query();
        $query->filter = $filter;
        $query->query = new Criterion\MatchAll();
        $query->offset = 0;
        $query->limit = 1;

        try {
            $result = $this->contentSearchService->execute($query, 'content', $languageFilter);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());

            return parent::findSingle($filter, $languageFilter);
        }

        if ($result->totalCount < 1) {
            throw new NotFoundException('Content', 'findSingle() found no content for the given $filter');
        }

        if ($result->totalCount > 1) {
            throw new InvalidArgumentException(
                'totalCount',
                'findSingle() found more then one Content item for the given $filter'
            );
        }

        return reset($result->searchHits)->valueObject;
    }

    public function findContent(Query $query, array $languageFilter = []): SearchResult
    {
        try {
            return $this->contentSearchService->execute($query, 'content', $languageFilter);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());

            return parent::findContent($query, $languageFilter);
        }
    }

    public function findLocations(LocationQuery $query, array $languageFilter = []): SearchResult
    {
        if (!isset($languageFilter['languages'])) {
            $languageFilter['languages'] = ['eng-GB'];
        }

        try {
            return $this->locationSearchService->execute($query, 'location', $languageFilter);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());

            return parent::findLocations($query, $languageFilter);
        }
    }
}
