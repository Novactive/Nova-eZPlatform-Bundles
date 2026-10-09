<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Taxonomy;

use DateTime;

class IbexaTaxonomyData
{
    public const IMPORT_MODE_CREATE_ONLY = 0;
    public const IMPORT_MODE_ONLY_UPDATE = 1;
    public const IMPORT_MODE_UPDATE_AND_CREATE_IF_NOT_EXISTS = 2;
    public const IMPORT_MODE_FETCH_ONLY = 3;

    protected string $identifier;
    protected string $parentIdentifier;
    /** @var array<string, string> */
    protected array $names = [];
    protected string $taxonomyName;
    protected string $contentRemoteId;
    protected ?int $ownerId = null;
    protected ?int $sectionId = null;
    protected int|null|DateTime $modificationDate = null;
    protected string $mainLanguageCode = 'eng-GB';
    protected int $importMode = self::IMPORT_MODE_UPDATE_AND_CREATE_IF_NOT_EXISTS;

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function getParentIdentifier(): string
    {
        return $this->parentIdentifier;
    }

    public function setParentIdentifier(string $parentIdentifier): void
    {
        $this->parentIdentifier = $parentIdentifier;
    }

    /**
     * @return array<string, string>
     */
    public function getNames(): array
    {
        return $this->names;
    }

    /**
     * @param array<string,string> $names
     */
    public function setNames(array $names): void
    {
        $this->names = $names;
    }

    public function getTaxonomyName(): string
    {
        return $this->taxonomyName;
    }

    public function setTaxonomyName(string $taxonomyName): void
    {
        $this->taxonomyName = $taxonomyName;
    }

    public function getContentRemoteId(): string
    {
        return $this->contentRemoteId;
    }

    public function setContentRemoteId(string $contentRemoteId): void
    {
        $this->contentRemoteId = $contentRemoteId;
    }

    public function getOwnerId(): ?int
    {
        return $this->ownerId;
    }

    public function setOwnerId(?int $ownerId): void
    {
        $this->ownerId = $ownerId;
    }

    public function getSectionId(): ?int
    {
        return $this->sectionId;
    }

    public function setSectionId(?int $sectionId): void
    {
        $this->sectionId = $sectionId;
    }

    public function getModificationDate(): DateTime|int|null
    {
        return $this->modificationDate;
    }

    public function setModificationDate(DateTime|int|null $modificationDate): void
    {
        $this->modificationDate = $modificationDate;
    }

    public function getMainLanguageCode(): string
    {
        return $this->mainLanguageCode;
    }

    public function setMainLanguageCode(string $mainLanguageCode): void
    {
        $this->mainLanguageCode = $mainLanguageCode;
    }

    public function getImportMode(): int
    {
        return $this->importMode;
    }

    public function setImportMode(int $importMode): void
    {
        $this->importMode = $importMode;
    }
}
