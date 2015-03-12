<?php
/**
 * NovaeZExtraBundle PreContentViewListener
 *
 * @package   Novactive\Bundle\eZExtraBundle
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZSEOBundle/blob/master/LICENSE MIT Licence
 */
namespace Novactive\Bundle\eZExtraBundle\EventListener;

use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Repository;
use Novactive\Bundle\eZExtraBundle\Core\Helper\eZ\Content as ContentHelper;

/**
 * Class Type
 */
abstract class Type
{

    /**
     * The Content view
     *
     * @var ContentView
     */
    protected $contentView;

    /**
     * The Location
     *
     * @var Location
     */
    protected $location;

    /**
     * Repository eZ
     *
     * @var Repository
     */
    protected $repository;

    /**
     * Content Helper
     *
     * @var ContentHelper
     */
    protected $contentHelper;

    /**
     * Set the Content View
     *
     * @param ContentView $contentView
     */
    public function setContentView( ContentView $contentView )
    {
        $this->contentView = $contentView;
    }

    /**
     * Set the Location
     *
     * @param Location $location
     */
    public function setLocation( Location $location )
    {
        $this->location = $location;
    }

    /**
     * Constructor
     *
     * @param Repository    $repository
     * @param ContentHelper $contentHelper
     */
    public function __construct( Repository $repository, ContentHelper $contentHelper )
    {
        $this->repository    = $repository;
        $this->contentHelper = $contentHelper;
    }

    /**
     * Get the Children
     *
     * @param array $viewParameters
     *
     * @return mixed
     */
    abstract public function getChildren( $viewParameters );
}
