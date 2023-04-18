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

declare(strict_types=1);

namespace Novactive\Bundle\eZExtraBundle\EventListener;

use Novactive\Bundle\eZExtraBundle\Core\Helper\eZ\Result;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\ChainConfigResolver as ConfigResolver;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\View\ContentView;
use Novactive\Bundle\eZExtraBundle\Core\Helper\eZ\Content as ContentHelper;
use Novactive\Bundle\eZExtraBundle\Core\Helper\eZ\WrapperFactory;
use Symfony\Contracts\Service\Attribute\Required;

abstract class Type
{
    protected ContentView $contentView;
    protected Location $location;
    protected Content $content;
    protected Repository $repository;
    protected ContentHelper $contentHelper;
    protected ConfigResolver $configResolver;
    protected WrapperFactory $wrapperFactory;

    #[Required]
    public function setDependencies(
        Repository $repository,
        ContentHelper $contentHelper,
        ConfigResolver $configResolver,
        WrapperFactory $wrapperFactory
    ): void {
        $this->repository = $repository;
        $this->contentHelper = $contentHelper;
        $this->configResolver = $configResolver;
        $this->wrapperFactory = $wrapperFactory;
    }

    public function setContentView(ContentView $contentView): void
    {
        $this->contentView = $contentView;
    }

    public function setLocation(Location $location): void
    {
        $this->location = $location;
    }

    public function setContent(Content $content): void
    {
        $this->content = $content;
    }

    /**
     * @deprecated Now use dynamic children instead.
     *             Example : for full view children build a method getFullChildren
     */
    public function getChildren(array $viewParameters, ?SiteAccess $siteAccess = null): array|Result
    {
        return [];
    }
}
