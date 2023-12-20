<?php

/**
 * NovaeZExtraBundle RouterAware.
 *
 * @package   Novactive\Bundle\eZExtraBundle
 *
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZExtraBundle\Contracts;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\MVC\Symfony\Routing\UrlAliasRouter;
use Novactive\Bundle\eZExtraBundle\Core\Helper\eZ\Wrapper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

trait RouterAware
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @required
     */
    public function setRouter(RouterInterface $router): void
    {
        $this->router = $router;
    }

    public function generateRouteLocation(Location $location): string
    {
        return $this->router->generate(
            UrlAliasRouter::URL_ALIAS_ROUTE_NAME,
            ['locationId' => $location->id, 'contentId' => $location->contentId]
        );
    }

    public function generateRouteContent(Content $content): string
    {
        return $this->router->generate(
            UrlAliasRouter::URL_ALIAS_ROUTE_NAME,
            ['locationId' => $content->contentInfo->mainLocationId, 'contentId' => $content->id]
        );
    }

    public function generateRouteWrapper(Wrapper $wrapper): string
    {
        return $this->generateRouteLocation($wrapper->location);
    }

    public function generate(
        string $name,
        array $parameters = [],
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string {
        return $this->router->generate($name, $parameters, $referenceType);
    }
}
