<?php

/**
 * NovaeZStaticTemplatesBundle.
 *
 * @package   Novactive\Bundle\EzStaticTemplatesBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZStaticTemplatesBundle/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Novactive\Bundle\EzStaticTemplatesBundle\Routing;

use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use eZ\Publish\Core\MVC\Symfony\SiteAccess\SiteAccessAware;
use Symfony\Cmf\Component\Routing\ChainedRouterInterface;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route as SymfonyRoute;
use Symfony\Component\Routing\RouteCollection;
use Twig\Environment;

class Router implements ChainedRouterInterface, RequestMatcherInterface, SiteAccessAware
{
    /**
     * @var array
     */
    protected $siteAccessGroups;

    /**
     * @var SiteAccess
     */
    protected $siteAccess;

    /**
     * @var RequestContext
     */
    protected $context;

    /**
     * @var Environment
     */
    protected $twig;

    public function __construct(array $siteAccessGroups, Environment $twig)
    {
        $this->siteAccessGroups = $siteAccessGroups;
        $this->twig = $twig;
    }

    public function setSiteAccess(SiteAccess $siteAccess = null): void
    {
        $this->siteAccess = $siteAccess;
    }

    public function matchRequest(Request $request): array
    {
        if (
            !isset($this->siteAccessGroups['static_group']) ||
            !\in_array($this->siteAccess->name, $this->siteAccessGroups['static_group'])
        ) {
            throw new ResourceNotFoundException();
        }
        $requestedPath = rawurldecode($request->attributes->get('semanticPathinfo', $request->getPathInfo()));
        $requestedPath = trim($requestedPath, '/');

        $params = [
            '_route' => 'static_template',
            '_controller' => function (string $template = 'index') {
                return new Response($this->twig->render("@ibexadesign/{$template}.html.twig"));
            },
        ];
        if (!empty($requestedPath)) {
            $params['template'] = $requestedPath;
        }

        return $params;
    }

    public function getContext(): RequestContext
    {
        return $this->context;
    }

    public function setContext(RequestContext $context): void
    {
        $this->context = $context;
    }

    public function getRouteCollection(): RouteCollection
    {
        return new RouteCollection();
    }

    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH)
    {
        // nothing to do
    }

    public function match($pathinfo)
    {
        // nothing to do
    }

    public function supports($name): bool
    {
        return 'maquette' === $name;
    }

    public function getRouteDebugMessage($name, array $parameters = [])
    {
        if ($name instanceof RouteObjectInterface) {
            return 'Route with key '.$name->getRouteKey();
        }

        if ($name instanceof SymfonyRoute) {
            return 'Route with pattern '.$name->getPath();
        }

        return $name;
    }
}
