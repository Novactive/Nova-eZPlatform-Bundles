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
use Novactive\Bundle\eZExtraBundle\Core\Helper\eZ\Search as SearchHelper;

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
     * Search Helper
     *
     * @var SearchHelper
     */
    protected $searchHelper;

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
     * @param SearchHelper  $searchHelper
     */
    public function __construct( Repository $repository, ContentHelper $contentHelper, SearchHelper $searchHelper )
    {
        $this->repository    = $repository;
        $this->contentHelper = $contentHelper;
        $this->searchHelper  = $searchHelper;
    }

    /**
     * Get the Children
     *
     * @deprecated Now use dynamic children instead.
     *             Example : for full view children build a method getFullChildren
     *
     * @param array $viewParameters
     *
     * @return mixed
     */
    abstract public function getChildren( $viewParameters );
}
