<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Content;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentStruct;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;

class AbstractIbexaContentHandler
{
    protected Repository $repository;

    public function __construct(
        Repository $repository
    ) {
        $this->repository = $repository;
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
