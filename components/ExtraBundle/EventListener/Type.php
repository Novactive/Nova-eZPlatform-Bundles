<?php

/**
 * NovaeZExtraBundle PreContentViewListener.
 *
 * @package   Novactive\Bundle\eZExtraBundle
 *
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */

namespace Novactive\Bundle\eZExtraBundle\EventListener;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ChainConfigResolver as ConfigResolver;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use Novactive\Bundle\eZExtraBundle\Core\Helper\eZ\Content as ContentHelper;
use Novactive\Bundle\eZExtraBundle\Core\Helper\eZ\WrapperFactory;

/**
 * Class Type.
 */
abstract class Type
{
    /**
     * The Content view.
     *
     * @var ContentView
     */
    protected $contentView;

    /**
     * The Location.
     *
     * @var Location
     */
    protected $location;

    /**
     * The Content.
     *
     * @var Content
     */
    protected $content;

    /**
     * Repository eZ.
     *
     * @var Repository
     */
    protected $repository;

    /**
     * Content Helper.
     *
     * @var ContentHelper
     */
    protected $contentHelper;

    /**
     * Config resolver.
     *
     * @var ConfigResolver
     */
    protected $configResolver;

    /**
     * WrapperFactory.
     *
     * @var WrapperFactory
     */
    protected $wrapperFactory;

    /**
     * Set the Content View.
     */
    public function setContentView(ContentView $contentView)
    {
        $this->contentView = $contentView;
    }

    /**
     * Set the Location.
     */
    public function setLocation(Location $location)
    {
        $this->location = $location;
    }

    /**
     * Set the Content.
     */
    public function setContent(Content $content)
    {
        $this->content = $content;
    }

    /**
     * Set the WrapperFactory.
     *
     * @param WrapperFactory $wrapperFactory wrapperFactory
     *
     * @return $this
     */
    public function setWrapperFactory($wrapperFactory)
    {
        $this->wrapperFactory = $wrapperFactory;

        return $this;
    }

    /**
     * Set the Repository.
     *
     * @param Repository $repository repository
     *
     * @return $this
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * Set the ContentHelper.
     *
     * @param ContentHelper $contentHelper contentHelper
     *
     * @return $this
     */
    public function setContentHelper($contentHelper)
    {
        $this->contentHelper = $contentHelper;

        return $this;
    }

    /**
     * Set the ConfigResolver.
     *
     * @param ConfigResolver $configResolver configResolver
     *
     * @return $this
     */
    public function setConfigResolver($configResolver)
    {
        $this->configResolver = $configResolver;

        return $this;
    }

    /**
     * Get the Children.
     *
     * @deprecated Now use dynamic children instead.
     *             Example : for full view children build a method getFullChildren
     *
     * @param array      $viewParameters
     * @param SiteAccess $siteAccess
     *
     * @return array
     */
    public function getChildren($viewParameters, SiteAccess $siteAccess = null)
    {
        return [];
    }
}
