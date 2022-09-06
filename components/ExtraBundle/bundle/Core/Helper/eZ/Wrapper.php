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

declare(strict_types=1);

namespace Novactive\Bundle\eZExtraBundle\Core\Helper\eZ;

use ArrayAccess;
use Exception;
use Ibexa\Contracts\Core\Repository\Exceptions\PropertyNotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\PropertyReadOnlyException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content as ValueContent;
use Ibexa\Contracts\Core\Repository\Values\Content\Location as ValueLocation;

/**
 * @property ValueLocation $location
 * @property ValueContent  $content
 */
final class Wrapper implements ArrayAccess
{
    /**
     * @var ValueContent
     */
    private $content;

    /**
     * @var ValueLocation
     */
    private $location;

    /**
     * Extra Data.
     */
    private $extraData;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var int
     */
    private $locationId;

    /**
     * @var int
     */
    private $contentId;

    /**
     * @param ValueContent|int  $contentId
     * @param ValueLocation|int $locationId
     */
    public function __construct($contentId = null, $locationId = null, array $extraData = [])
    {
        if (null === $contentId && null === $locationId) {
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

    public function setRepository(Repository $repository): self
    {
        $this->repository = $repository;

        return $this;
    }

    public function hasExtraData(): bool
    {
        return null !== $this->extraData;
    }

    public function setExtraData(array $data): self
    {
        $this->extraData = $data;

        return $this;
    }

    public function getExtraData(): array
    {
        return $this->extraData;
    }

    public function location(): ValueLocation
    {
        return $this->getLocation();
    }

    public function content(): ValueContent
    {
        return $this->getContent();
    }

    public function getLocation(): ValueLocation
    {
        if ($this->location instanceof ValueLocation) {
            return $this->location;
        }
        if (null === $this->locationId && $this->contentId > 0) {
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

    public function getContent(): ValueContent
    {
        if ($this->content instanceof ValueContent) {
            return $this->content;
        }
        if (!$this->content instanceof ValueContent) {
            if (null === $this->contentId && $this->locationId > 0) {
                $this->content = $this->getLocation()->getContent();
            } else {
                $this->content = $this->repository->getContentService()->loadContent($this->contentId);
            }
        }

        return $this->content;
    }

    /**
     * Getter.
     *
     * @throws PropertyNotFoundException
     *
     * @return ValueContent|ValueLocation
     */
    public function __get(string $name)
    {
        switch ($name) {
            case 'content':
                return $this->getContent();
            case 'location':
                return $this->getLocation();
        }
        throw new PropertyNotFoundException("Can't find property: ".__CLASS__."->{$name}");
    }

    /**
     * Setter.
     *
     * @throws PropertyReadOnlyException
     */
    public function __set(string $name, $value): void
    {
        throw new PropertyReadOnlyException("Can't set property: ".__CLASS__."->{$name}");
    }

    public function offsetExists($offset): bool
    {
        return property_exists($this, $offset);
    }

    public function offsetGet($offset)
    {
        if (property_exists($this, $offset)) {
            return $this->$offset();
        }

        return null;
    }

    public function offsetSet($offset, $value): void
    {
        throw new PropertyReadOnlyException("Can't set property: ".__CLASS__."[{$offset}]");
    }

    public function offsetUnset($offset): void
    {
        throw new PropertyReadOnlyException("Can't unset property: ".__CLASS__."[{$offset}]");
    }
}
