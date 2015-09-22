<?php
/**
 * NovaeZExtraBundle Wrapper
 *
 * @package   Novactive\Bundle\eZExtraBundle
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZSEOBundle/blob/master/LICENSE MIT Licence
 */

namespace Novactive\Bundle\eZExtraBundle\Core\Helper\eZ;

use eZ\Publish\API\Repository\Exceptions\PropertyNotFoundException;
use eZ\Publish\API\Repository\Exceptions\PropertyReadOnlyException;
use eZ\Publish\API\Repository\Values\Content\Content as ValueContent;
use eZ\Publish\API\Repository\Values\Content\Location as ValueLocation;

/**
 * Class Wrapper
 */
class Wrapper implements \ArrayAccess
{
    /**
     * The Content
     *
     * @var ValueContent
     */
    public $content;

    /**
     * The Location
     *
     * @var ValueLocation
     */
    public $location;

    /**
     * Extra Data
     *
     * @var mixed
     */
    protected $extraData;

    /**
     * Constructor
     *
     * @param ValueContent  $content
     * @param ValueLocation $location
     * @param mixed         $extraData
     */
    public function __construct( ValueContent $content, ValueLocation $location, $extraData = null )
    {
        $this->content   = $content;
        $this->location  = $location;
        $this->extraData = $extraData;
    }

    /**
     * Has Extra
     */
    public function hasExtraData()
    {
        return $this->extraData !== null;
    }

    /**
     * Get the ExtraData
     *
     * @return mixed
     */
    public function getExtraData()
    {
        return $this->extraData;
    }

    /**
     * Getter
     *
     * @param string $name
     *
     * @return ValueContent|ValueLocation
     * @throws PropertyNotFoundException
     */
    public function __get( $name )
    {
        if ( property_exists( $this, $name ) )
        {
            return $this->$name;
        }
        throw new PropertyNotFoundException( "Can't find property: " . __CLASS__ . "->{$name}" );
    }

    /**
     * Setter
     *
     * @param string $name
     * @param mixed  $value
     *
     * @throws PropertyReadOnlyException
     */
    public function __set( $name, $value )
    {
        throw new PropertyReadOnlyException( "Can't set property: " . __CLASS__ . "->{$name}" );
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists( $offset )
    {
        return property_exists( $this, $offset );
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet( $offset )
    {
        if ( property_exists( $this, $offset ) )
        {
            return $this->$offset;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet( $offset, $value )
    {
        throw new PropertyReadOnlyException( "Can't set property: " . __CLASS__ . "[{$offset}]" );
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset( $offset )
    {
        throw new PropertyReadOnlyException( "Can't unset property: " . __CLASS__ . "[{$offset}]" );
    }
}
