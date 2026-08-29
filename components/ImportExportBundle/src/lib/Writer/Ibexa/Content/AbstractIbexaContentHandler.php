<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Content;

use AlmaviaCX\Bundle\IbexaImportExport\Writer\Utils\Checksum;
use ErdnaxelaWeb\ContentAdditionalInformations\Service\ContentAdditionalInformationsService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentStruct;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;

class AbstractIbexaContentHandler
{
    public function __construct(
        protected Repository $repository,
        protected ContentAdditionalInformationsService $contentAdditionalInformationsService
    ) {
    }

    protected function doContentNeedUpdate(Content $content, Checksum $checksum): bool
    {
        try {
            $existingChecksumValue = $this->contentAdditionalInformationsService->get(
                $content->id,
                $content->versionInfo->versionNo,
                $checksum->identifier
            )->getValue();
        } catch (\Ibexa\Core\Base\Exceptions\NotFoundException $notFoundException) {
            return true;
        }

        return $existingChecksumValue !== $checksum->value;
    }

    protected function saveContentChecksum(Content $content, Checksum $checksum): void
    {
        if (null === $checksum->value) {
            $this->contentAdditionalInformationsService->delete(
                $content->id,
                $content->versionInfo->versionNo,
                $checksum->identifier
            );
        } else {
            $this->contentAdditionalInformationsService->set(
                $content->id,
                $content->versionInfo->versionNo,
                $checksum->identifier,
                $checksum->value
            );
        }
    }

    /**
     * @param array<string, mixed> $fieldsByLanguages
     */
    protected function setContentFields(
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
}
