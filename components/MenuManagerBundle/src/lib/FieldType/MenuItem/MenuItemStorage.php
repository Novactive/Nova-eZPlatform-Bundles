<?php

/**
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Novactive\EzMenuManager\FieldType\MenuItem;

use Ibexa\Contracts\Core\FieldType\GatewayBasedStorage;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;

class MenuItemStorage extends GatewayBasedStorage
{
    public function storeFieldData(VersionInfo $versionInfo, Field $field, array $context = [])
    {
        // VersionInfo return STATUS_DRAFT when publish Content
        // TODO: Store data field when content published
        // if (VersionInfo::STATUS_PUBLISHED === $versionInfo->status || 1 == $versionInfo->versionNo) {
        return $this->gateway->storeFieldData($versionInfo, $field);
        // }
    }

    public function getFieldData(VersionInfo $versionInfo, Field $field, array $context = [])
    {
        if (VersionInfo::STATUS_PUBLISHED === $versionInfo->status) {
            return $this->gateway->getFieldData($versionInfo, $field);
        }
    }

    public function deleteFieldData(VersionInfo $versionInfo, array $fieldIds, array $context = [])
    {
        if (VersionInfo::STATUS_PUBLISHED === $versionInfo->status) {
            return $this->gateway->deleteFieldData($versionInfo, $fieldIds);
        }
    }

    public function hasFieldData()
    {
        return true;
    }

    public function getIndexData(VersionInfo $versionInfo, Field $field, array $context)
    {
        return false;
    }

    public function copyLegacyField(VersionInfo $versionInfo, Field $field, Field $originalField, array $context = [])
    {
        return $this->storeFieldData($versionInfo, $field, $context);
    }
}
