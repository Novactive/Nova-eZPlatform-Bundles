<?php
/**
 * NovaeZExtraBundle Wrapper Factory
 *
 * @package   Novactive\Bundle\eZExtraBundle
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */

namespace Novactive\Bundle\eZExtraBundle\Core\Helper\eZ;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content as ValueContent;
use eZ\Publish\API\Repository\Values\Content\Location as ValueLocation;

/**
 * Class WrapperFactory
 */
class WrapperFactory
{

    /**
     * Repository eZ
     *
     * @var Repository
     */
    protected $repository;

    /**
     * Constructor
     *
     * @param Repository $eZRepo
     */
    public function __construct(Repository $eZRepo)
    {
        $this->repository = $eZRepo;
    }

    /**
     * Create
     *
     * @param ValueContent|integer  $contentId
     * @param ValueLocation|integer $locationId
     * @param mixed                 $extra
     *
     * @return Wrapper
     */
    public function create($contentId, $locationId, $extra = null)
    {
        $wrapper = new Wrapper($contentId, $locationId, $extra);

        return $wrapper->setRepository($this->repository);
    }

    /**
     * CreateByLocation
     *
     * @param ValueLocation $location
     *
     * @return Wrapper
     */
    public function createByLocation(ValueLocation $location)
    {
        $wrapper = new Wrapper($location->contentInfo->id, $location);

        return $wrapper->setRepository($this->repository);
    }

    /**
     * CreateByLocationID
     *
     * @param integer $locationId
     *
     * @return Wrapper
     */
    public function createByLocationId($locationId)
    {
        $wrapper = new Wrapper(null, $locationId);

        return $wrapper->setRepository($this->repository);
    }

    /**
     * CreateByContent
     *
     * @param ValueContent $content
     *
     * @return Wrapper
     */
    public function createByContent(ValueContent $content)
    {
        $wrapper = new Wrapper($content, $content->contentInfo->mainLocationId);

        return $wrapper->setRepository($this->repository);
    }

    /**
     * CreateByContentId
     *
     * @param integer $contentId
     *
     * @return Wrapper
     */
    public function createByContentId($contentId)
    {
        $wrapper = new Wrapper($contentId);

        return $wrapper->setRepository($this->repository);
    }
}
