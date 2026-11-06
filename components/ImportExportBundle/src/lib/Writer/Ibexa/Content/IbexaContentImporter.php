<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Content;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;

class IbexaContentImporter
{
    public function __construct(
        protected Repository $repository,
        protected IbexaContentUpdater $contentUpdater,
        protected IbexaContentCreator $contentCreator
    ) {
    }

    /**
     * @param \AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Content\IbexaContentData $contentData
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     *
     * @return array{action: ?string, content: Content}|null
     */
    public function __invoke(IbexaContentData $contentData): ?array
    {
        $remoteId = $contentData->getContentRemoteId();
        $ownerId = $contentData->getOwnerId();
        if (null === $ownerId) {
            $ownerId = $this->repository
                ->getPermissionResolver()
                ->getCurrentUserReference()
                ->getUserId();
        }

        try {
            try {
                $content = $this->repository->getContentService()->loadContentByRemoteId(
                    $contentData->getContentRemoteId()
                );

                if (IbexaContentData::IMPORT_MODE_DELETE === $contentData->getImportMode()) {
                    $this->repository->getContentService()->deleteContent($content->contentInfo);

                    return [
                        'action' => 'delete',
                        'content' => $content,
                    ];
                }

                if (
                    !in_array($contentData->getImportMode(), [
                    IbexaContentData::IMPORT_MODE_ONLY_UPDATE,
                    IbexaContentData::IMPORT_MODE_UPDATE_AND_CREATE_IF_NOT_EXISTS,
                    ])
                ) {
                    return [
                        'action' => null,
                        'content' => $content,
                    ];
                }

                $publishedContent = ($this->contentUpdater)(
                    $content,
                    $contentData->getFields(),
                    $contentData->getParentLocationIdList(),
                    $contentData->getChecksum(),
                    $ownerId,
                    $contentData->getMainLanguageCode(),
                    $contentData->isHidden(),
                    $contentData->isAllowMoveOnUpdate()
                );

                $didUpdate = $publishedContent->versionInfo->versionNo !== $content->versionInfo->versionNo;

                return [
                    'action' => $didUpdate ? 'update' : 'ignored',
                    'content' => $publishedContent,
                ];
            } catch (NotFoundException $exception) {
                if (
                    !in_array($contentData->getImportMode(), [
                    IbexaContentData::IMPORT_MODE_CREATE_ONLY,
                    IbexaContentData::IMPORT_MODE_UPDATE_AND_CREATE_IF_NOT_EXISTS,
                    ])
                ) {
                    return null;
                }

                $content = ($this->contentCreator)(
                    $contentData->getContentTypeIdentifier(),
                    $contentData->getParentLocationIdList(),
                    $contentData->getFields(),
                    $remoteId,
                    $contentData->getChecksum(),
                    $ownerId,
                    $contentData->getMainLanguageCode(),
                    $contentData->getSectionId(),
                    $contentData->getModificationDate(),
                    $contentData->isHidden()
                );

                return [
                    'action' => 'create',
                    'content' => $content,
                ];
            }
        } catch (\Throwable $exception) {
            dd($exception, $contentData);
            throw $exception;
        }
    }
}
