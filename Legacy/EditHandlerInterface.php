<?php
/**
 * NovaeZExtraBundle EditHandler
 *
 * @package   Novactive\Bundle\eZExtraBundle
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */
namespace Novactive\Bundle\eZExtraBundle\Legacy;

/**
 * Class EditHandlerInterface
 */
interface EditHandlerInterface
{
    /**
     * Validate form input
     *
     * @param \eZHTTPTool $http
     * @param $module
     * @param \eZContentClass $class
     * @param \eZContentObject $object
     * @param \eZContentObjectVersion $version
     * @param $contentObjectAttributes
     * @param $editVersion
     * @param $editLanguage
     * @param $fromLanguage
     * @param $validationParameters
     * @return mixed
     */
    public function validateInput( $http, &$module, &$class, $object, &$version, $contentObjectAttributes, $editVersion, $editLanguage, $fromLanguage, $validationParameters );

    /**
     * We can store extra data here. And retrieve this data when publishing (create|update)
     *
     * @param \eZHTTPTool $http
     * @param $module
     * @param \eZContentClass $class
     * @param \eZContentObject $object
     * @param \eZContentObjectVersion $version
     * @param $contentObjectAttributes
     * @param $editVersion
     * @param $editLanguage
     * @param $fromLanguage
     * @param $validationParameters
     * @return mixed
     */
    public function prePublish( $http, $module, $class, $object, $version, $contentObjectAttributes, $editVersion, $editLanguage, $fromLanguage, $validationParameters );

    /**
     * @param \eZContentObject $object
     * @return mixed
     */
    public function update( $object );

    /**
     * @param \eZContentObject $object
     * @return mixed
     */
    public function create( $object );
}
