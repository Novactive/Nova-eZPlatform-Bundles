<?php
/**
 * NovaeZExtraBundle Tag Manager
 *
 * @package   Novactive\Bundle\eZExtraBundle
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
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
     * @var int
     */
    protected $eZTagsVersion;

    /**
     * Constructor
     *
     * @param TagsService $service
     */
    public function __construct(TagsService $service)
    {
        $this->eZTagsService = $service;
        $this->eZTagsVersion = 1;
        if (method_exists('Netgen\TagsBundle\API\Repository\Values\Tags\TagCreateStruct', 'setKeyword')) {
            $this->eZTagsVersion = 2;
        }
    }

    /**
     * Is it eZ Tags 2
     *
     * @return bool
     */
    protected function iseZTagsV2()
    {
        return $this->eZTagsVersion == 2;
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
     * @param string  $lang
     *
     * @return Tag
     */
    public function createTag($parentTagId, $name, $options = [], $lang = 'eng-US')
    {
        if ($this->iseZTagsV2()) {
            $tagStruct                   = $this->eZTagsService->newTagCreateStruct($parentTagId, $lang);
            $tagStruct->mainLanguageCode = $lang;
            $tagStruct->setKeyword($name);
        } else {
            $tagStruct = $this->eZTagsService->newTagCreateStruct($parentTagId, $name);
        }

        if (!empty($options['remoteId'])) {
            $tagStruct->remoteId = $options['remoteId'];
        }

        return $this->eZTagsService->createTag($tagStruct);
    }

    /**
     * Update the keyword/name of the tag
     *
     * @param TagValue $tag
     * @param string   $name
     * @param string   $lang
     *
     * @return TagValue
     */
    public function updateTag(TagValue $tag, $name, $lang = 'eng-US')
    {
        $tagUpdateStruct = $this->eZTagsService->newTagUpdateStruct();
        if ($this->iseZTagsV2()) {
            $tagUpdateStruct->setKeyword($name, $lang);
        } else {
            $tagUpdateStruct->keyword = $name;
        }
        $this->eZTagsService->updateTag($tag, $tagUpdateStruct);

        return $tag;
    }

    /**
     * Create/Update Sugar for trying to update else create
     *
     * @param integer $parentTagId
     * @param string  $name
     * @param string  $remoteId
     * @param array   $options
     * @param string  $lang
     *
     * @return TagValue
     */
    public function createUpdateTag($parentTagId, $name, $remoteId, $options = [], $lang = 'eng-US')
    {
        $options['remoteId'] = $remoteId;
        try {
            $tag = $this->eZTagsService->loadTagByRemoteId($remoteId);
            if (array_key_exists('do_no_update', $options) && ($options['do_no_update'] == true)) {
                return $tag;
            }
            $newTag = $this->updateTag($tag, $name, $lang);
            if ((array_key_exists('callback_update', $options)) && (is_callable($options['callback_update']))) {
                $options['callback_update']($newTag);
            }
        } catch (NotFoundException $e) {
            $newTag = $this->createTag($parentTagId, $name, $options, $lang);
            if ((array_key_exists('callback_create', $options)) && (is_callable($options['callback_create']))) {
                $options['callback_create']($newTag);
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
     * @param string  $lang
     *
     * @return TagValue
     */
    public function getCreateTag($parentTagId, $name, $remoteId, $options = [], $lang = 'eng-US')
    {
        $options['do_no_update'] = true;

        return $this->createUpdateTag($parentTagId, $name, $remoteId, $options, $lang);
    }
}
