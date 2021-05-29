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

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use Novactive\Bundle\eZExtraBundle\Core\Helper\eZ\Wrapper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

trait RouterAware
{
    private RouterInterface $router;

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
            'ez_urlalias',
            ['locationId' => $location->id, 'contentId' => $location->contentId]
        );
    }

    public function generateRouteContent(Content $content): string
    {
        return $this->router->generate(
            'ez_urlalias',
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
