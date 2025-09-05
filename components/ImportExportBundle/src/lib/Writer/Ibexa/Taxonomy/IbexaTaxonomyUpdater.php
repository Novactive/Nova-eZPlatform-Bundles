<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Taxonomy;

use AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Content\IbexaContentUpdater;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\FieldType\TextLine\Value as TextLineValue;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;

class IbexaTaxonomyUpdater
{
    protected TaxonomyServiceInterface $taxonomyService;
    protected TaxonomyConfiguration $taxonomyConfiguration;
    protected IbexaContentUpdater $contentUpdater;

    public function __construct(
        TaxonomyServiceInterface $taxonomyService,
        TaxonomyConfiguration $taxonomyConfiguration,
        IbexaContentUpdater $contentUpdater,
    ) {
        $this->contentUpdater = $contentUpdater;
        $this->taxonomyConfiguration = $taxonomyConfiguration;
        $this->taxonomyService = $taxonomyService;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function __invoke(
        TaxonomyEntry $entry,
        TaxonomyEntry $parent,
        array $names,
        ?int $ownerId = null,
        string $mainLanguageCode = 'eng-GB',
        bool $hidden = false
    ): TaxonomyEntry {
        if ($entry->parent->identifier !== $parent->identifier) {
            $this->taxonomyService->moveEntry($entry, $parent);
        }

        $fields = [];
        foreach ($names as $languageCode => $name) {
            $fields[$languageCode] = [
                'name' => new TextLineValue($name),
            ];
        }

        $content = ($this->contentUpdater)(
            $entry->getContent(),
            $fields,
            [$parent->content->contentInfo->getMainLocation()],
            $ownerId,
            $mainLanguageCode,
            $hidden
        );

        return $this->taxonomyService->loadEntryByContentId($content->id);
    }
}
