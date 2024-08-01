<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Content;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;

class IbexaContentUpdater extends AbstractIbexaContentHandler
{
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
    public function __invoke(
        Content $content,
        array $fieldsByLanguages,
        int $ownerId = null,
        string $mainLanguageCode = 'eng-GB'
    ): Content {
        $contentType = $this->repository->getContentTypeService()->loadContentType(
            $content->contentInfo->contentTypeId
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
}
