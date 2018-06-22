<?php
/**
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 */

namespace Novactive\EzMenuManager\FieldType\MenuItem;

use eZ\Publish\SPI\FieldType\GatewayBasedStorage;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;
use Novactive\EzMenuManager\FieldType\MenuItem\MenuItemStorage\Gateway;

class MenuItemStorage extends GatewayBasedStorage
{
    /**
     * @var Gateway
     */
    protected $gateway;

    /**
     * @param VersionInfo $versionInfo
     * @param Field       $field
     * @param array       $context
     *
     * @return mixed
     */
    public function storeFieldData(VersionInfo $versionInfo, Field $field, array $context)
    {
        return $this->gateway->storeFieldData($versionInfo, $field);
    }

    /**
     * @param VersionInfo $versionInfo
     * @param Field       $field
     * @param array       $context
     *
     * @return mixed
     */
    public function getFieldData(VersionInfo $versionInfo, Field $field, array $context)
    {
        return $this->gateway->getFieldData($versionInfo, $field);
    }

    /**
     * @param VersionInfo $versionInfo
     * @param array       $fieldIds
     * @param array       $context
     *
     * @return bool
     */
    public function deleteFieldData(VersionInfo $versionInfo, array $fieldIds, array $context)
    {
        return $this->gateway->deleteFieldData($versionInfo, $fieldIds);
    }

    /**
     * @return bool
     */
    public function hasFieldData()
    {
        return true;
    }

    /**
     * @param VersionInfo $versionInfo
     * @param Field       $field
     * @param array       $context
     *
     * @return bool|\eZ\Publish\SPI\Search\Field[]
     */
    public function getIndexData(VersionInfo $versionInfo, Field $field, array $context)
    {
        return false;
    }
}
