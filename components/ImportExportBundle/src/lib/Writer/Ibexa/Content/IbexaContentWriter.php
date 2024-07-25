<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Content;

use AlmaviaCX\Bundle\IbexaImportExport\Accessor\Ibexa\ObjectAccessorBuilder;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\ItemTransformer;
use AlmaviaCX\Bundle\IbexaImportExport\Item\Transformer\SourceResolver;
use AlmaviaCX\Bundle\IbexaImportExport\Reference\ReferenceBag;
use AlmaviaCX\Bundle\IbexaImportExport\Writer\AbstractWriter;
use DateTime;
use Exception;
use Ibexa\Contracts\Core\Repository\Exceptions\BadStateException;
use Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Base\Exceptions\ContentValidationException;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Translation\TranslatableMessage;

class IbexaContentWriter extends AbstractWriter implements TranslationContainerInterface
{
    protected Repository $repository;
    protected ObjectAccessorBuilder $objectAccessorBuilder;

    public function __construct(
        Repository $repository,
        ObjectAccessorBuilder $objectAccessorBuilder,
        SourceResolver $sourceResolver,
        ItemTransformer $itemTransformer,
        ReferenceBag $references
    ) {
        $this->repository = $repository;
        $this->objectAccessorBuilder = $objectAccessorBuilder;
        parent::__construct($sourceResolver, $itemTransformer, $references);
    }

    protected function getMappedItemInstance()
    {
        return new IbexaContentData();
    }

    /**
     * {@inheritDoc}
     *
     * @param \AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Content\IbexaContentData $mappedItem
     */
    protected function writeItem($item, $mappedItem)
    {
        /** @var \AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Content\IbexaContentWriterOptions $options */
        $options = $this->getOptions();

        $content = $this->repository->sudo(function (Repository $repository) use ($item, $options, $mappedItem) {
            $remoteId = $mappedItem->getContentRemoteId();
            $ownerId = $mappedItem->getOwnerId();
            if (null === $ownerId) {
                $ownerId = $this->repository
                    ->getPermissionResolver()
                    ->getCurrentUserReference()
                    ->getUserId();
            }

            try {
                $content = $repository->getContentService()->loadContentByRemoteId($mappedItem->getContentRemoteId());
                if (!$options->allowUpdate) {
                    return $content;
                }

                return $this->updateContent(
                    $content,
                    $mappedItem->getFields(),
                    $ownerId,
                    $mappedItem->getMainLanguageCode()
                );
            } catch (InvalidArgumentType $exception) {
                dd($item, $mappedItem, $exception->getMessage());
            } catch (BadStateException $exception) {
                $this->logger->info('Removing content with remote id "'.$remoteId.'"');
                $repository->getContentService()->deleteContent($content->contentInfo);
            } catch (UnauthorizedException $exception) {
                $this->logger->info('Not authorized to load content with remote id "'.$remoteId.'"');
            } catch (NotFoundException $exception) {
                $this->logger->info('Creating new content with remote id "'.$remoteId.'"');

                try {
                    return $this->createContent(
                        $mappedItem->getContentTypeIdentifier(),
                        $mappedItem->getParentLocationIdList(),
                        $mappedItem->getFields(),
                        $remoteId,
                        $ownerId,
                        $mappedItem->getMainLanguageCode(),
                        $mappedItem->getSectionId(),
                        $mappedItem->getModificationDate()
                    );
                } catch (InvalidArgumentType $exception) {
                    $this->logger->info('----> '.get_class($exception));
                    $this->logger->info($exception->getMessage());
                    $this->logger->info(print_r($exception->getFile().' Line : '.$exception->getLine(), true));

                    throw $exception;
                } catch (ContentFieldValidationException $exception) {
                    dd($item, $mappedItem, $exception->getFieldErrors());
                    $newException = \Ibexa\Core\Base\Exceptions\ContentFieldValidationException::createNewWithMultiline(
                        $exception->getFieldErrors(),
                        $remoteId
                    );
                    $this->logger->info('----> '.get_class($newException));
                    $this->logger->info($newException->getMessage());
                    $this->logger->info(print_r($newException->getFieldErrors(), true));
                    $this->logger->info(print_r($newException->getTraceAsString(), true));

                    throw $newException;
                }
            } catch (ContentValidationException $exception) {
                $this->logger->info('----> '.get_class($exception));
                $this->logger->info($exception->getMessage());
                $this->logger->info(print_r($exception->getTraceAsString(), true));

                throw $exception;
            }
        });

        $imported_content_ids = $this->results->getResult('imported_content_ids');
        $imported_content_ids[] = $content->id;
        $this->results->setResult('imported_content_ids', $imported_content_ids);

        return $this->objectAccessorBuilder->buildFromContent($content);
    }

    /**
     * @param array<string, mixed> $fieldsByLanguages
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function updateContent(
        Content $content,
        array $fieldsByLanguages,
        int $ownerId = null,
        string $mainLanguageCode = 'eng-GB'
    ): Content {
        $contentType = $this->repository->getContentTypeService()->loadContentType(
            $content->contentInfo->contentTypeId
        );

        $this->logger->info(
            'Updating existing content "'.$content->contentInfo->name.'" ('.$content->contentInfo->remoteId.')'
        );

        $contentInfo = $content->contentInfo;
        $contentDraft = $this->repository->getContentService()->createContentDraft($contentInfo);

        /* Creating new content update structure */
        $contentUpdateStruct = $this->repository
            ->getContentService()
            ->newContentUpdateStruct();
        $contentUpdateStruct->initialLanguageCode = $mainLanguageCode; // set language for new version
        $contentUpdateStruct->creatorId = $ownerId;

        $this->setContentFields(
            $contentType,
            $contentUpdateStruct,
            $fieldsByLanguages,
        );

        $contentDraft = $this->repository->getContentService()->updateContent(
            $contentDraft->versionInfo,
            $contentUpdateStruct
        );

        /* Publish the new content draft */
        return $this->repository->getContentService()->publishVersion($contentDraft->versionInfo);
    }

    /**
     * @param array<string|int, int|string|Location> $parentLocationIdList
     * @param array<string, mixed>                   $fieldsByLanguages
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function createContent(
        string $contentTypeIdentifier,
        array $parentLocationIdList,
        array $fieldsByLanguages,
        string $remoteId,
        int $ownerId = null,
        string $languageCode = 'eng-GB',
        int $sectionId = null,
        $modificationDate = null,
        bool $hidden = false
    ): Content {
        $contentType = $this->repository->getContentTypeService()->loadContentTypeByIdentifier(
            $contentTypeIdentifier
        );

        /* Creating new content create structure */
        $contentCreateStruct = $this->repository->getContentService()->newContentCreateStruct(
            $contentType,
            $languageCode
        );
        $contentCreateStruct->remoteId = $remoteId;
        $contentCreateStruct->ownerId = $ownerId;
        if (null !== $modificationDate) {
            $contentCreateStruct->modificationDate = $modificationDate instanceof DateTime ?
                $modificationDate :
                DateTime::createFromFormat('U', (string) $modificationDate);
        }

        if ($sectionId) {
            $contentCreateStruct->sectionId = $sectionId;
        }

        /* Update content structure fields */
        $this->setContentFields($contentType, $contentCreateStruct, $fieldsByLanguages);

        /* Assigning the content locations */
        $locationCreateStructs = [];
        foreach ($parentLocationIdList as $locationRemoteId => $parentLocationId) {
            if (empty($parentLocationId)) {
                throw new Exception('Parent location id cannot be empty');
            }
            if ($parentLocationId instanceof Location) {
                $parentLocationId = $parentLocationId->id;
            }
            if (is_string($parentLocationId)) {
                $parentLocationId = $this->repository->getLocationService()->loadLocationByRemoteId(
                    $parentLocationId
                )->id;
            }
            $locationCreateStruct = $this->repository->getLocationService()->newLocationCreateStruct(
                $parentLocationId
            );
            if (is_string($locationRemoteId)) {
                $locationCreateStruct->remoteId = $locationRemoteId;
            }
            if ($hidden) {
                $locationCreateStruct->hidden = true;
            }
            $locationCreateStructs[] = $locationCreateStruct;
        }

        /* Creating new draft */
        $draft = $this->repository->getContentService()->createContent(
            $contentCreateStruct,
            $locationCreateStructs
        );

        /* Publish the new content draft */
        return $this->repository->getContentService()->publishVersion($draft->versionInfo);
    }

    /**
     * @param array<string, mixed> $fieldsByLanguages
     */
    public function setContentFields(
        ContentType $contentType,
        ContentStruct $contentStruct,
        array $fieldsByLanguages
    ): void {
        foreach ($fieldsByLanguages as $languageCode => $fields) {
            foreach ($fields as $fieldID => $field) {
                $fieldDefinition = $contentType->getFieldDefinition($fieldID);
                if ($fieldDefinition instanceof FieldDefinition) {
                    $contentStruct->setField($fieldID, $field, $languageCode);
                }
            }
        }
    }

    public static function getName(): TranslatableMessage
    {
        return new TranslatableMessage('writer.ibexa.content.name', [], 'import_export');
    }

    public static function getTranslationMessages(): array
    {
        return [( new Message('writer.ibexa.content.name', 'import_export') )->setDesc('Ibexa content writer')];
    }

    public static function getResultTemplate(): ?string
    {
        return '@ibexadesign/import_export/writer/results/writer_ibexa_content.html.twig';
    }
}
