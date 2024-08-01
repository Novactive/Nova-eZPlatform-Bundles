<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Content;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Repository;

class IbexaContentImporter
{
    protected Repository $repository;
    protected IbexaContentUpdater $contentUpdater;
    protected IbexaContentCreator $contentCreator;

    public function __construct(
        Repository $repository,
        IbexaContentUpdater $contentUpdater,
        IbexaContentCreator $contentCreator
    ) {
        $this->contentCreator = $contentCreator;
        $this->contentUpdater = $contentUpdater;
        $this->repository = $repository;
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
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Content
     */
    public function __invoke(IbexaContentData $contentData, bool $allowUpdate = true)
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
                if (!$allowUpdate) {
                    return $content;
                }

                return ($this->contentUpdater)(
                    $content,
                    $contentData->getFields(),
                    $ownerId,
                    $contentData->getMainLanguageCode()
                );
            } catch (NotFoundException $exception) {
                return ($this->contentCreator)(
                    $contentData->getContentTypeIdentifier(),
                    $contentData->getParentLocationIdList(),
                    $contentData->getFields(),
                    $remoteId,
                    $ownerId,
                    $contentData->getMainLanguageCode(),
                    $contentData->getSectionId(),
                    $contentData->getModificationDate()
                );
            }
        } catch (\Throwable $exception) {
            dump($exception);
            throw $exception;
        }
    }
}
