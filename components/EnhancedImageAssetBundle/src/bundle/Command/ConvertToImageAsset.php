<?php

/**
 * NovaeZEnhancedImageAssetBundle.
 *
 * @package   NovaeZEnhancedImageAssetBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZEnhancedImageAssetBundle/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Novactive\EzEnhancedImageAssetBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use Exception;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Exceptions\BadStateException;
use eZ\Publish\API\Repository\Exceptions\ContentTypeFieldDefinitionValidationException;
use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException as APINotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeDraft;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionCreateStruct;
use eZ\Publish\Core\Base\Exceptions\ContentFieldValidationException;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentValue;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\FieldType\ImageAsset\Value as ImageAssetValue;
use eZ\Publish\Core\Repository\ContentTypeService;
use eZ\Publish\Core\SignalSlot\SignalDispatcher;
use Novactive\EzEnhancedImageAsset\FieldValueConverter\ChainFieldValueConverter;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @SuppressWarnings(PHPMD)
 */
class ConvertToImageAsset extends Command
{
    /** @var Connection */
    protected $connection;

    /** @var Repository */
    protected $repository;

    /** @var ContentTypeService */
    protected $contentTypeService;

    /** @var ContentService */
    protected $contentService;

    /** @var ChainFieldValueConverter */
    protected $valueConverter;

    /** @var SignalDispatcher */
    protected $signalDispatcher;

    /** @var TagAwareAdapterInterface */
    protected $cache;

    /** @var SymfonyStyle */
    protected $io;

    /**
     * @required
     */
    public function setConnection(Connection $connection): void
    {
        $this->connection = $connection;
    }

    /**
     * @required
     */
    public function setRepository(Repository $repository): void
    {
        $this->repository = $repository;
    }

    /**
     * @required
     */
    public function setContentTypeService(ContentTypeService $contentTypeService): void
    {
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * @required
     */
    public function setContentService(ContentService $contentService): void
    {
        $this->contentService = $contentService;
    }

    /**
     * @required
     */
    public function setValueConverter(ChainFieldValueConverter $valueConverter): void
    {
        $this->valueConverter = $valueConverter;
    }

    /**
     * @required
     */
    public function setSignalDispatcher(SignalDispatcher $signalDispatcher): void
    {
        $this->signalDispatcher = $signalDispatcher;
    }

    /**
     * @required
     */
    public function setCache(TagAwareAdapterInterface $cache): void
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(): void
    {
        $this->setName('ezenhancedimageasset:convert:field_to_imageasset')
            ->addArgument(
                'field_identifiers',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'content_type_identifier/field_identifier',
                []
            );
    }

    /**
     * {@inheritDoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        parent::initialize($input, $output);
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $this->repository->sudo(
            function () use ($input) {
                /** @var array $fieldIdentifiers */
                $fieldIdentifiers = $input->getArgument('field_identifiers');
                foreach ($fieldIdentifiers as $fieldIdentifier) {
                    [$contentTypeIdentifier, $fieldIdentifier] = explode('/', $fieldIdentifier);

                    $contentType = $this->contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);
                    $fieldDefinition = $contentType->getFieldDefinition($fieldIdentifier);
                    if (null === $fieldDefinition) {
                        throw new NotFoundException(FieldDefinition::class, $fieldIdentifier);
                    }
                    $this->io->comment("Converting {$contentTypeIdentifier}/{$fieldIdentifier}");

                    if ('ezimageasset' === $fieldDefinition->fieldTypeIdentifier) {
                        $this->io->success("Field {$contentTypeIdentifier}/{$fieldIdentifier} is already converted");
                        continue;
                    }

                    $newFieldDefinitionCreateStruct = $this->getNewFieldDefinitionCreateStruct($fieldDefinition);
                    if (!$contentType->getFieldDefinition($newFieldDefinitionCreateStruct->identifier)) {
                        $draft = $this->getContentTypeDraft($contentType);
                        $this->contentTypeService->addFieldDefinition($draft, $newFieldDefinitionCreateStruct);
                        $this->contentTypeService->publishContentTypeDraft($draft);
                        $contentType = $this->contentTypeService->loadContentTypeByIdentifier($contentType->identifier);
                    }
                    $newFieldDefinition = $contentType->getFieldDefinition($newFieldDefinitionCreateStruct->identifier);

                    $this->updateContents($contentType, $fieldDefinition, $newFieldDefinition);

                    $draft = $this->getContentTypeDraft($contentType);
                    try {
                        $this->contentTypeService->removeFieldDefinition($draft, $fieldDefinition);
                    } catch (InvalidArgumentException $e) {
                        $this->io->warning($e->getMessage());
                    }
                    $newFieldDefinitionUpdateStruct = $this->contentTypeService->newFieldDefinitionUpdateStruct();

                    $newFieldDefinitionUpdateStruct->identifier = $fieldDefinition->identifier;
                    $this->contentTypeService->updateFieldDefinition(
                        $draft,
                        $newFieldDefinition,
                        $newFieldDefinitionUpdateStruct
                    );
                    $this->contentTypeService->publishContentTypeDraft($draft);

                    $this->io->success('Conversion done');
                }
            }
        );

        return null;
    }

    /**
     * @throws InvalidArgumentValue
     */
    protected function getNewFieldDefinitionCreateStruct(
        FieldDefinition $originalFieldDefinition
    ): FieldDefinitionCreateStruct {
        $createStruct = $this->contentTypeService->newFieldDefinitionCreateStruct(
            $this->assetFieldIdentifier($originalFieldDefinition),
            'ezimageasset'
        );

        $createStruct->names = $originalFieldDefinition->getNames();
        $createStruct->descriptions = $originalFieldDefinition->getDescriptions();
        $createStruct->position = $originalFieldDefinition->position;
        $createStruct->isRequired = $originalFieldDefinition->isRequired;
        $createStruct->isSearchable = $originalFieldDefinition->isSearchable;
        $createStruct->isInfoCollector = $originalFieldDefinition->isInfoCollector;
        $createStruct->isTranslatable = $originalFieldDefinition->isTranslatable;
        $createStruct->fieldGroup = $originalFieldDefinition->fieldGroup;

        return $createStruct;
    }

    protected function assetFieldIdentifier(FieldDefinition $originalFieldDefinition): string
    {
        return sprintf('%s_asset', $originalFieldDefinition->identifier);
    }

    /**
     * @throws APINotFoundException
     * @throws UnauthorizedException
     */
    protected function getContentTypeDraft(ContentType $contentType): ContentTypeDraft
    {
        try {
            return $this->contentTypeService->createContentTypeDraft($contentType);
        } catch (BadStateException $e) {
            return $this->contentTypeService->loadContentTypeDraft($contentType->id);
        }
    }

    /**
     * @throws InvalidArgumentException
     * @throws APINotFoundException
     * @throws UnauthorizedException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    protected function updateContents(
        ContentType $contentType,
        FieldDefinition $originalFieldDefinition,
        FieldDefinition $fieldDefinition
    ): void {
        $query = $this->connection->createQueryBuilder();
        $query->from('ezcontentobject', 'o')
            ->where($query->expr()->eq('o.contentclass_id', ':contentclass_id'))
            ->setParameter(':contentclass_id', $contentType->id, ParameterType::INTEGER);

        $countQuery = clone $query;
        $countQuery->select('count(*)');
        $totalCount = $countQuery->execute()->fetch(FetchMode::COLUMN);
        $progressBar = new ProgressBar($this->io, $totalCount);

        $batch = 500;
        $offset = 0;
        $query->select('id');
        $query->setMaxResults($batch);
        $query->setFirstResult(0);

        do {
            $query->setFirstResult($offset);
            $contentIds = $query->execute()->fetchAll(FetchMode::COLUMN);
            foreach ($contentIds as $contentId) {
                $contentInfo = $this->contentService->loadContentInfo($contentId);
                $this->updateContent($contentInfo, $originalFieldDefinition, $fieldDefinition);
                $progressBar->advance();
            }

            $offset += $batch;
        } while (count($contentIds) === $batch);
        $progressBar->finish();
        $progressBar->clear();
    }

    /**
     * @throws InvalidArgumentException
     * @throws APINotFoundException
     * @throws UnauthorizedException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    protected function updateContent(
        ContentInfo $contentInfo,
        FieldDefinition $originalFieldDefinition,
        FieldDefinition $fieldDefinition
    ): void {
        $versions = $this->contentService->loadVersions($contentInfo);
        $invalidateCache = false;
        foreach ($versions as $versionInfo) {
            $content = $this->contentService->loadContent(
                $contentInfo->id,
                $versionInfo->languageCodes,
                $versionInfo->versionNo
            );
            foreach ($versionInfo->languageCodes as $languageCode) {
                $newField = $content->getField($fieldDefinition->identifier, $languageCode);
                if (null !== $newField && null !== $newField->value->destinationContentId) {
                    continue;
                }

                $originalField = $content->getField($originalFieldDefinition->identifier, $languageCode);
                if (null === $originalField) {
                    continue;
                }
                try {
                    $imageAssetValue = $this->valueConverter->toImageAssetValue($content, $originalField);
                    if ($imageAssetValue && null !== $imageAssetValue->destinationContentId) {
                        $this->updateField(
                            $content,
                            $fieldDefinition,
                            $versionInfo,
                            $imageAssetValue,
                            $languageCode
                        );
                        $invalidateCache = true;
                    }
                } catch (ContentFieldValidationException $e) {
                    $this->io->error(
                        "Can't convert value for version {$versionInfo->versionNo} of content {$contentInfo->id}"
                    );
                }
            }
        }
        if ($invalidateCache) {
            $this->cache->invalidateTags(['content-'.$contentInfo->id]);
        }
    }

    protected function updateField(
        Content $content,
        FieldDefinition $fieldDefinition,
        VersionInfo $version,
        ImageAssetValue $value,
        string $languageCode
    ): void {
        $query = $this->connection->createQueryBuilder();
        $query->update('ezcontentobject_attribute', 'oa')
            ->set('oa.data_float', ':null')
            ->set('oa.data_int', ':destination_content_id')
            ->set('oa.data_text', ':alternative_text')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('oa.contentclassattribute_id', ':contentclassattribute_id'),
                    $query->expr()->eq('oa.contentobject_id', ':contentobject_id'),
                    $query->expr()->eq('oa.version', ':attribute_version'),
                    $query->expr()->eq('oa.language_code', ':language_code')
                )
            )
            ->setParameter(':null', null, ParameterType::NULL)
            ->setParameter(':contentclassattribute_id', $fieldDefinition->id, ParameterType::INTEGER)
            ->setParameter(':contentobject_id', $content->id, ParameterType::INTEGER)
            ->setParameter(':attribute_version', $version->versionNo, ParameterType::INTEGER)
            ->setParameter(':language_code', $languageCode, ParameterType::STRING)
            ->setParameter(':destination_content_id', $value->destinationContentId, ParameterType::INTEGER)
            ->setParameter(':alternative_text', $value->alternativeText, ParameterType::STRING);
        $query->execute();
    }

    /**
     * @throws BadStateException
     * @throws ContentTypeFieldDefinitionValidationException
     * @throws InvalidArgumentException
     * @throws InvalidArgumentValue
     * @throws APINotFoundException
     * @throws UnauthorizedException
     */
    protected function updateContentType(ContentType $contentType, FieldDefinition $fieldDefinition): FieldDefinition
    {
        $newFieldCreateStruct = $this->getNewFieldDefinitionCreateStruct($fieldDefinition);
        if (!$contentType->getFieldDefinition($newFieldCreateStruct->identifier)) {
            $draft = $this->contentTypeService->createContentTypeDraft($contentType);
            $this->contentTypeService->addFieldDefinition($draft, $newFieldCreateStruct);
            $this->contentTypeService->publishContentTypeDraft($draft);
            $contentType = $this->contentTypeService->loadContentTypeByIdentifier($contentType->identifier);
        }

        return $contentType->getFieldDefinition($newFieldCreateStruct->identifier);
    }
}
