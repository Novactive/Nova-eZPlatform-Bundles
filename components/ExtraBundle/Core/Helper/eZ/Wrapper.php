<?php

/**
 * NovaeZExtraBundle Wrapper.
 *
 * @package   Novactive\Bundle\eZExtraBundle
 *
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */

namespace Novactive\Bundle\eZExtraBundle\Core\Helper\eZ;

use Exception;
use eZ\Publish\API\Repository\Exceptions\PropertyNotFoundException;
use eZ\Publish\API\Repository\Exceptions\PropertyReadOnlyException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content as ValueContent;
use eZ\Publish\API\Repository\Values\Content\Location as ValueLocation;

/**
 * Class Wrapper.
 *
 * @property ValueLocation $location
 * @property ValueContent  $content
 */
class Wrapper implements \ArrayAccess
{
    /**
     * The Content.
     *
     * @var ValueContent
     */
    protected $content;

    /**
     * The Location.
     *
     * @var ValueLocation
     */
    protected $location;

    /**
     * Extra Data.
     */
    protected $extraData;

    /**
     * Repository eZ.
     *
     * @var Repository
     */
    protected $repository;

    /**
     * Location Id.
     *
     * @var int
     */
    protected $locationId;

    /**
     * Content Id.
     *
     * @var int
     */
    protected $contentId;

    /**
     * Constructor.
     *
     * @param ValueContent|int $contentId
     */
    public function __construct($contentId = null, $locationId = null, $extraData = null)
    {
        if (null == $contentId && null == $locationId) {
            throw new Exception('NovaExtraWrapper: you must provide at least contentId or locationId');
        }

        $this->contentId = $contentId;
        $this->locationId = $locationId;

        // Ensure the backward compatibility
        if ($contentId instanceof ValueContent) {
            $this->contentId = $contentId->id;
            $this->content = $contentId;
            if (null === $locationId) {
                $this->locationId = $contentId->contentInfo->mainLocationId;
            }
        }
        if ($locationId instanceof ValueLocation) {
            $this->locationId = $locationId->id;
            $this->contentId = $locationId->contentInfo->id;
            $this->location = $locationId;
        }
        $this->extraData = $extraData;
    }

    /**
     * Set the eZ Repository.
     *
     * @return $this
     */
    public function setRepository(Repository $repository)
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * Has Extra.
     */
    public function hasExtraData()
    {
        return null !== $this->extraData;
    }

    /**
     * Set Extra Data.
     *
     * @return $this
     */
    public function setExtraData($data)
    {
        $this->extraData = $data;

        return $this;
    }

    /**
     * Get the ExtraData.
     */
    public function getExtraData()
    {
        return $this->extraData;
    }

    /**
     * Get the Location.
     *
     * @return ValueLocation
     */
    public function location()
    {
        return $this->getLocation();
    }

    /**
     * Get the Content.
     *
     * @return ValueContent
     */
    public function content()
    {
        return $this->getContent();
    }

    /**
     * Get the Location.
     *
     * @return ValueLocation
     */
    public function getLocation()
    {
        if ($this->location instanceof ValueLocation) {
            return $this->location;
        }
        if (null == $this->locationId && $this->contentId > 0) {
            $this->location = $this->repository->getLocationService()->loadLocation(
                $this->getContent()->contentInfo->mainLocationId
            );
        } else {
            $this->location = $this->repository->getLocationService()->loadLocation(
                $this->locationId
            );
        }

        return $this->location;
    }

    /**
     * Get the Content.
     *
     * @return ValueContent
     */
    public function getContent()
    {
        if ($this->content instanceof ValueContent) {
            return $this->content;
        }
        if (!$this->content instanceof ValueContent) {
            if (null == $this->contentId && $this->locationId > 0) {
                $this->content = $this->repository->getContentService()->loadContent(
                    $this->getLocation()->contentInfo->id
                );
            } else {
                $this->content = $this->repository->getContentService()->loadContent($this->contentId);
            }
        }

        return $this->content;
    }

    /**
     * Getter.
     *
     * @param string $name
     *
     * @throws PropertyNotFoundException
     *
     * @return ValueContent|ValueLocation
     */
    public function __get($name)
    {
        switch ($name) {
            case 'content':
                return $this->getContent();
                break;
            case 'location':
                return $this->getLocation();
                break;
        }
        throw new PropertyNotFoundException("Can't find property: ".__CLASS__."->{$name}");
    }

    /**
     * Setter.
     *
     * @param string $name
     *
     * @throws PropertyReadOnlyException
     */
    public function __set($name, $value)
    {
        throw new PropertyReadOnlyException("Can't set property: ".__CLASS__."->{$name}");
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return property_exists($this, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        if (property_exists($this, $offset)) {
            return $this->$offset();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        throw new PropertyReadOnlyException("Can't set property: ".__CLASS__."[{$offset}]");
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        throw new PropertyReadOnlyException("Can't unset property: ".__CLASS__."[{$offset}]");
    }
}
