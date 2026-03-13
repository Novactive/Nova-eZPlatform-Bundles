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

use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessAware;
use RuntimeException;
use Symfony\Cmf\Component\Routing\ChainedRouterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Twig\Environment;

class Router implements ChainedRouterInterface, RequestMatcherInterface, SiteAccessAware
{
    public const string ROUTE_NAME = 'static_template';

    protected SiteAccess $siteAccess;

    protected RequestContext $context;

    /**
     * @param array<string, mixed> $siteAccessGroups
     */
    public function __construct(
        protected array $siteAccessGroups,
        protected Environment $twig
    ) {
    }

    public function setSiteAccess(?SiteAccess $siteAccess = null): void
    {
        $this->siteAccess = $siteAccess;
    }

    /**
     * @return array<string, mixed>
     */
    public function matchRequest(Request $request): array
    {
        if (
            !isset($this->siteAccessGroups['static_group']) ||
            !\in_array($this->siteAccess->name, $this->siteAccessGroups['static_group'])
        ) {
            throw new ResourceNotFoundException();
        }
        $requestedPath = rawurldecode(
            (string) $request->attributes->get('semanticPathinfo', $request->getPathInfo())
        );
        $requestedPath = trim($requestedPath, '/');

        $params = [
            '_route' => self::ROUTE_NAME,
            '_controller' => fn (string $template = 'index') => new Response(
                $this->twig->render("@ibexadesign/{$template}.html.twig")
            ),
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

    /**
     * @param array<string, scalar> $parameters
     */
    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        if($name === self::ROUTE_NAME) {
            $template = $parameters['template'];
            unset($parameters['template']);
            $query = http_build_query($parameters);
            $linkUri = "$template?$query";

            if ($this->siteAccess->matcher instanceof SiteAccess\URILexer) {
                return $this->siteAccess->matcher->analyseLink($linkUri);
            }
        }
        throw new RouteNotFoundException('Could not match route');
    }

    /**
     * @return array<string, mixed>
     */
    public function match(string $pathinfo): array
    {
        throw new RuntimeException(
            'The '.static::class." doesn't support the match() method. Use matchRequest() instead."
        );
    }

    public function getRouteDebugMessage(string $name, array $parameters = []): string
    {
        return $name;
    }
}
