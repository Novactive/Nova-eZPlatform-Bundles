<?php
/**
 * NovaeZExtraBundle Tag Manager
 *
 * @package   Novactive\Bundle\eZExtraBundle
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZSEOBundle/blob/master/LICENSE MIT Licence
 */
namespace Novactive\Bundle\eZExtraBundle\Core\Manager\eZ;

use Netgen\TagsBundle\API\Repository\TagsService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag as TagValue;

/**
 * Class Tag
 */
class Tag
{
    /**
     * Tag eZ Tag Service
     *
     * @var TagsService
     */
    protected $eZTagsService;

    /**
     * Constructor
     *
     * @param TagsService $service
     */
    public function __construct( TagsService $service )
    {
        $this->eZTagsService = $service;
    }

    /**
     * Get eZ Repository
     *
     * @return TagsService
     */
    public function getTagsService()
    {
        return $this->eZTagsService;
    }

    /**
     * Create a new Tag Wrapper
     *
     * @param integer $parentTagId
     * @param string  $name
     * @param array   $options
     *
     * @return Tag
     */
    public function createTag( $parentTagId, $name, $options = [] )
    {
        $tagStruct = $this->eZTagsService->newTagCreateStruct( $parentTagId, $name );
        if ( !empty( $options['remoteId'] ) )
        {
            $tagStruct->remoteId = $options['remoteId'];
        }

        return $this->eZTagsService->createTag( $tagStruct );
    }

    /**
     * Update the keyword/name of the tag
     *
     * @param TagValue $tag
     * @param string   $name
     *
     * @return TagValue
     */
    public function updateTag( TagValue $tag, $name )
    {
        $tagUpdateStruct          = $this->eZTagsService->newTagUpdateStruct();
        $tagUpdateStruct->keyword = $name;

        $this->eZTagsService->updateTag( $tag, $tagUpdateStruct );

        return $tag;
    }

    /**
     * Create/Update Sugar for trying to update else create
     *
     * @param integer $parentTagId
     * @param string  $name
     * @param string  $remoteId
     * @param array   $options
     *
     * @return TagValue
     */
    public function createUpdateTag( $parentTagId, $name, $remoteId, $options = [] )
    {
        $options['remoteId'] = $remoteId;
        try
        {
            $tag = $this->eZTagsService->loadTagByRemoteId( $remoteId );
            if ( array_key_exists( 'do_no_update', $options ) && ( $options['do_no_update'] == true ) )
            {
                return $tag;
            }
            $newTag = $this->updateTag( $tag, $name );
            if ( ( array_key_exists( 'callback_update', $options ) ) && ( is_callable( $options['callback_update'] ) ) )
            {
                $options['callback_update']( $newTag );
            }
        }
        catch ( NotFoundException $e )
        {
            $newTag = $this->createTag( $parentTagId, $name, $options );
            if ( ( array_key_exists( 'callback_create', $options ) ) && ( is_callable( $options['callback_create'] ) ) )
            {
                $options['callback_create']( $newTag );
            }
        }

        return $newTag;
    }

    /**
     * Get/Create Sugar for trying to get else create
     *
     * @param integer $parentTagId
     * @param string  $name
     * @param string  $remoteId
     * @param array   $options
     *
     * @return TagValue
     */
    public function getCreateTag( $parentTagId, $name, $remoteId, $options = [] )
    {
        $options['do_no_update'] = true;

        return $this->createUpdateTag( $parentTagId, $name, $remoteId, $options );
    }
}
