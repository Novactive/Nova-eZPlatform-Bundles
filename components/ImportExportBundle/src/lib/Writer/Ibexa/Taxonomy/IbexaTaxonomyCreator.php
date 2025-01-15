<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Taxonomy;

use AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Content\IbexaContentCreator;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\FieldType\TextLine\Value as TextLineValue;
use Ibexa\Taxonomy\FieldType\TaxonomyEntry\Value as TaxonomyEntryValue;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;

class IbexaTaxonomyCreator
{
    protected TaxonomyServiceInterface $taxonomyService;
    protected TaxonomyConfiguration $taxonomyConfiguration;
    protected IbexaContentCreator $contentCreator;

    public function __construct(
        TaxonomyServiceInterface $taxonomyService,
        TaxonomyConfiguration $taxonomyConfiguration,
        IbexaContentCreator $contentCreator,
    ) {
        $this->contentCreator = $contentCreator;
        $this->taxonomyConfiguration = $taxonomyConfiguration;
        $this->taxonomyService = $taxonomyService;
    }

    /**
     * @param null $modificationDate
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Taxonomy\Exception\TaxonomyConfigurationNotFoundException
     * @throws \Ibexa\Taxonomy\Exception\TaxonomyEntryNotFoundException
     * @throws \Ibexa\Taxonomy\Exception\TaxonomyNotFoundException
     */
    public function __invoke(
        string $identifier,
        TaxonomyEntry $parent,
        array $names,
        string $remoteId,
        int $ownerId = null,
        string $mainLanguageCode = 'eng-GB',
        int $sectionId = null,
        $modificationDate = null,
        bool $hidden = false
    ): TaxonomyEntry {
        $contentTypeIdentifier = $this->taxonomyConfiguration->getConfigForTaxonomy(
            $parent->taxonomy,
            TaxonomyConfiguration::CONFIG_CONTENT_TYPE
        );

        $fields = [];
        foreach ($names as $languageCode => $name) {
            $fieldsForLanguage = [
                'name' => new TextLineValue($name),
            ];

            if ('fre-FR' === $languageCode) {
                $fieldsForLanguage['identifier'] = new TextLineValue($identifier);
                $fieldsForLanguage['parent'] = new TaxonomyEntryValue($parent);
            }

            $fields[$languageCode] = $fieldsForLanguage;
        }

        $content = ($this->contentCreator)(
            $contentTypeIdentifier,
            [$parent->content->contentInfo->getMainLocation()],
            $fields,
            $remoteId,
            $ownerId,
            $mainLanguageCode,
            $sectionId,
            $modificationDate,
            $hidden
        );

        return $this->taxonomyService->loadEntryByContentId($content->id);
    }
}
