<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Taxonomy;

class IbexaTaxonomyData
{
    protected string $identifier;
    protected string $parentIdentifier;
    /** @var array<string, mixed> */
    protected array $names = [];
    protected string $taxonomyName;
    protected string $contentRemoteId;
    protected ?int $ownerId = null;
    protected ?int $sectionId = null;
    protected int|\DateTime|null $modificationDate = null;
    protected string $mainLanguageCode = 'eng-GB';

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

    public function getNames(): array
    {
        return $this->names;
    }

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

    public function getModificationDate(): \DateTime|int|null
    {
        return $this->modificationDate;
    }

    public function setModificationDate(\DateTime|int|null $modificationDate): void
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
}
