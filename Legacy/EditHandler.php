<?php
/**
 * NovaeZExtraBundle EditHandler
 *
 * @package   Novactive\Bundle\eZExtraBundle
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZSEOBundle/blob/master/LICENSE MIT Licence
 */
namespace Novactive\Bundle\eZExtraBundle\Legacy;

abstract class EditHandler extends \eZContentObjectEditHandler
{
    const BASE_SERVICE_NAME = "novactive.ezextra.edithandler.%s";

    /**
     * @param $http
     * @param $module
     * @param $class
     * @param $object
     * @param $version
     * @param $contentObjectAttributes
     * @param $editVersion
     * @param $editLanguage
     * @param $fromLanguage
     * @param $validationParameters
     * @return array|void
     */
    function validateInput( $http, &$module, &$class, $object, &$version, $contentObjectAttributes, $editVersion, $editLanguage, $fromLanguage, $validationParameters ) {
        $res = ['is_valid' => true, 'warnings' =>[]];
        $contentClass = $object->attribute( 'content_class' );
        $identifierClass = $contentClass->attribute( 'identifier' );
        $service = $this->getService( $identifierClass );
        if ( $service ) {
            $res = $service->validateInput( $http, $module, $class, $object, $version, $contentObjectAttributes, $editVersion, $editLanguage, $fromLanguage, $validationParameters );
            if ( isset($res['is_valid']) && $res['is_valid'] == true ) {
                // store extra data during prePublish
                $service->prePublish( $http, $module, $class, $object, $version, $contentObjectAttributes, $editVersion, $editLanguage, $fromLanguage, $validationParameters );
            }
            return $res;
        }
        return $res;
    }

    /**
     * @param $contentObjectID
     * @param $contentObjectVersion
     */
    function publish( $contentObjectID, $contentObjectVersion ) {
        // fetch object
        $object = \eZContentObject::fetch( $contentObjectID );
        // get content class object
        $contentClass = $object->attribute( 'content_class' );
        $identifierClass = $contentClass->attribute( 'identifier' );
        $service = $this->getService( $identifierClass );
        if ( $service ) {
            try {
                if ( $contentObjectVersion > 1 ) {
                    $service->update( $object );
                } else {
                    $service->create( $object );
                }
            } catch( Exception $e ) {
                //@todo: LOG exception
            }
        }
    }

    /**
     * Try to find edithandler service composed by identifierClass
     *
     * @param $identifierClass
     * @return null|EditHandlerInterface
     */
    protected function getService( $identifierClass ) {
        $serviceName = sprintf( self::BASE_SERVICE_NAME, $identifierClass );
        $container = \ezpKernel::instance()->getServiceContainer();

        if ( $container->has($serviceName) ) {
            $service = $container->get($serviceName);
            if ( $service instanceof EditHandlerInterface ) {
                return $service;
            }
        }
        return null;
    }
}
