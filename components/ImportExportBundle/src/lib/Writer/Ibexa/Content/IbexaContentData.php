<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Writer\Ibexa\Content;

use AlmaviaCX\Bundle\IbexaImportExport\Writer\Utils\Checksum;
use DateTime;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;

class IbexaContentData
{
    public const IMPORT_MODE_CREATE_ONLY = 0;
    public const IMPORT_MODE_ONLY_UPDATE = 1;
    public const IMPORT_MODE_UPDATE_AND_CREATE_IF_NOT_EXISTS = 2;
    public const IMPORT_MODE_FETCH_ONLY = 3;
    public const IMPORT_MODE_DELETE = 4;

    protected string $contentRemoteId;
    /** @var array<string, mixed> */
    protected array $fields = [];
    protected string $mainLanguageCode = 'eng-GB';
    protected ?int $ownerId = null;
    protected ?string $contentTypeIdentifier = null;
    /** @var array<string|int, int|string|Location> */
    protected array $parentLocationIdList = [2];
    protected ?int $sectionId = null;
    protected int|null|DateTime $modificationDate = null;
    protected bool|null $hidden = null;
    protected int $importMode = self::IMPORT_MODE_UPDATE_AND_CREATE_IF_NOT_EXISTS;
    protected bool $allowMoveOnUpdate = false;
    protected Checksum $checksum;

    public function __construct()
    {
        $this->checksum = new Checksum();
    }

    public function getContentRemoteId(): string
    {
        return $this->contentRemoteId;
    }

    public function setContentRemoteId(string $contentRemoteId): void
    {
        $this->contentRemoteId = $contentRemoteId;
    }

    /**
     * @return array<string, mixed>
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param array<string, mixed> $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function getMainLanguageCode(): string
    {
        return $this->mainLanguageCode;
    }

    public function setMainLanguageCode(string $mainLanguageCode): void
    {
        $this->mainLanguageCode = $mainLanguageCode;
    }

    public function getOwnerId(): ?int
    {
        return $this->ownerId;
    }

    public function setOwnerId(?int $ownerId): void
    {
        $this->ownerId = $ownerId;
    }

    public function getContentTypeIdentifier(): ?string
    {
        return $this->contentTypeIdentifier;
    }

    public function setContentTypeIdentifier(?string $contentTypeIdentifier): void
    {
        $this->contentTypeIdentifier = $contentTypeIdentifier;
    }

    /**
     * @return array<string|int, int|string|Location>
     */
    public function getParentLocationIdList(): array
    {
        return $this->parentLocationIdList;
    }

    /**
     * @param array<string|int, int|string|Location> $parentLocationIdList
     */
    public function setParentLocationIdList(array $parentLocationIdList): void
    {
        $this->parentLocationIdList = $parentLocationIdList;
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

    public function isHidden(): bool|null
    {
        return $this->hidden;
    }

    public function setHidden(bool|null $hidden): void
    {
        $this->hidden = $hidden;
    }

    public function getImportMode(): int
    {
        return $this->importMode;
    }

    public function setImportMode(int $importMode): void
    {
        $this->importMode = $importMode;
    }

    public function isAllowMoveOnUpdate(): bool
    {
        return $this->allowMoveOnUpdate;
    }

    public function setAllowMoveOnUpdate(bool $allowMoveOnUpdate): void
    {
        $this->allowMoveOnUpdate = $allowMoveOnUpdate;
    }

    public function getChecksum(): Checksum
    {
        return $this->checksum;
    }

    public function setChecksum(Checksum $checksum): void
    {
        $this->checksum = $checksum;
    }
}
