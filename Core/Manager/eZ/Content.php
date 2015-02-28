<?php
/**
 * NovaeZExtraBundle Content Manager
 *
 * @package   Novactive\Bundle\eZExtraBundle
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZSEOBundle/blob/master/LICENSE MIT Licence
 */
namespace Novactive\Bundle\Core\Manager\eZ;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Content as ValueContent;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\API\Repository\Values\Content\ContentUpdateStruct;
use eZ\Publish\API\Repository\Values\Content\ContentCreateStruct;

/**
 * Class Content
 */
class Content
{
    /**
     * Repository eZ
     *
     * @var Repository
     */
    protected $eZPublishRepository;

    /**
     * Constructor
     *
     * @param Repository $api
     */
    public function __construct( Repository $api )
    {
        $this->eZPublishRepository = $api;
    }

    /**
     * Get eZ Repository
     *
     * @return Repository
     */
    public function getRepository()
    {
        return $this->eZPublishRepository;
    }

    /**
     * Easy access to the ContentService
     *
     * @return ContentService
     */
    public function getContentService()
    {
        return $this->eZPublishRepository->getContentService();
    }

    /**
     * Easy access to the ContentTypeService
     *
     * @return ContentTypeService
     */
    public function getContentTypeService()
    {
        return $this->eZPublishRepository->getContentTypeService();
    }

    /**
     * Easy access to the LocationService
     *
     * @return LocationService
     */
    public function getLocationService()
    {
        return $this->eZPublishRepository->getLocationService();
    }

    /**
     * Change the user of the repository
     * Note: you need to keep the current user if you want to go back on the current user
     */
    public function sudoRoot()
    {
        $this->eZPublishRepository->setCurrentUser( $this->eZPublishRepository->getUserService()->loadUser( 14 ) );
    }

    /**
     * Create Content Wrapper
     *
     * @param string $contentTypeIdentifier
     * @param int    $parentLocationId
     * @param array  $data
     * @param array  $options
     * @param string $lang
     *
     * @return ValueContent
     */
    public function createContent( $contentTypeIdentifier, $parentLocationId, $data, $options = [], $lang = 'eng-US' )
    {
        $contentService      = $this->getContentService();
        $contentType         = $this->getContentTypeService()->loadContentTypeByIdentifier( $contentTypeIdentifier );
        $contentCreateStruct = $contentService->newContentCreateStruct( $contentType, $lang );

        if ( !empty( $options['remoteId'] ) )
        {
            $contentCreateStruct->remoteId = $options['remoteId'];
        }

        if ( !empty( $options['sectionId'] ) )
        {
            $contentCreateStruct->sectionId = $options['sectionId'];
        }

        $this->autoFillStruct(
            $this->getContentTypeService()->loadContentTypeByIdentifier( $contentTypeIdentifier ),
            $contentCreateStruct,
            $data
        );

        $locationCreateStruct = $this->getLocationService()->newLocationCreateStruct( $parentLocationId );
        $draft                = $contentService->createContent( $contentCreateStruct, array( $locationCreateStruct ) );

        return $this->publishVersion( $draft );
    }

    /**
     * Update Content Wrapper
     *
     * @param ValueContent $content
     * @param array        $data
     * @param array        $options
     * @param string       $lang
     *
     * @return ValueContent
     */
    public function updateContent( ValueContent $content, $data, $options = [], $lang = 'eng-US' )
    {
        $contentService = $this->getContentService();

        $contentDraft                             = $contentService->createContentDraft( $content->contentInfo );
        $contentUpdateStruct                      = $contentService->newContentUpdateStruct();
        $contentUpdateStruct->initialLanguageCode = $lang;

        if ( !empty( $options['remoteId'] ) )
        {
            $contentUpdateStruct->remoteId = $options['remoteId'];
        }

        $this->autoFillStruct(
            $this->getContentTypeService()->loadContentType( $content->contentInfo->contentTypeId ),
            $contentUpdateStruct,
            $data
        );

        $contentDraft = $contentService->updateContent( $contentDraft->versionInfo, $contentUpdateStruct );

        return $this->publishVersion( $contentDraft );
    }

    /**
     * Publish a version wrapper
     *
     * @param ValueContent $draft
     *
     * @return ValueContent
     */
    protected function publishVersion( ValueContent $draft )
    {
        $content = $this->getContentService()->publishVersion( $draft->versionInfo );

        return $content;
    }

    /**
     * Autofill the Struct with the available field in $data
     *
     * @param ContentType $contentType
     * @param ValueObject $contentStruct
     * @param array       $data
     */
    protected function autoFillStruct( ContentType $contentType, ValueObject $contentStruct, $data )
    {
        /** @var ContentUpdateStruct|ContentUpdateStruct $contentStruct */

        foreach ( $contentType->getFieldDefinitions() as $field )
        {
            /** @var FieldDefinition $field */
            $fieldName = $field->identifier;
            if ( !array_key_exists( $fieldName, $data ) )
            {
                continue;
            }
            $fieldValue = $data[$fieldName];
            $contentStruct->setField( $fieldName, $fieldValue );
        }
    }
}
