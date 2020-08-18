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

namespace Novactive\Bundle\EzStaticTemplatesBundle\Routing;

use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use eZ\Publish\Core\MVC\Symfony\SiteAccess\SiteAccessAware;
use Symfony\Cmf\Component\Routing\ChainedRouterInterface;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route as SymfonyRoute;
use Symfony\Component\Routing\RouteCollection;

class Router implements ChainedRouterInterface, RequestMatcherInterface, SiteAccessAware
{
    /** @var array */
    protected $siteAccessGroups;

    /** @var SiteAccess */
    protected $siteAccess;

    /** @var RequestContext */
    protected $context;

    /**
     * Router constructor.
     *
     * @param array $siteAccessGroups
     */
    public function __construct(array $siteAccessGroups)
    {
        $this->siteAccessGroups = $siteAccessGroups;
    }

    /**
     * @param SiteAccess|null $siteAccess
     */
    public function setSiteAccess(SiteAccess $siteAccess = null)
    {
        $this->siteAccess = $siteAccess;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function matchRequest(Request $request)
    {
        if (!isset($this->siteAccessGroups['static_group']) || !in_array(
            $this->siteAccess->name,
            $this->siteAccessGroups['static_group']
        )) {
            throw new ResourceNotFoundException();
        }
        $requestedPath = rawurldecode($request->attributes->get('semanticPathinfo', $request->getPathInfo()));
        $requestedPath = trim($requestedPath, '/');

        $params = [
            '_route'      => 'static_template',
            '_controller' => 'EzStaticTemplatesBundle:EzStaticTemplates:index',
        ];
        if (!empty($requestedPath)) {
            $params['template'] = $requestedPath;
        }

        return $params;
    }

    /**
     * @return RequestContext
     */
    public function getContext(): RequestContext
    {
        return $this->context;
    }

    /**
     * @param RequestContext $context
     */
    public function setContext(RequestContext $context)
    {
        $this->context = $context;
    }

    public function getRouteCollection()
    {
        return new RouteCollection();
    }

    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH)
    {
        // TODO: Implement generate() method.
    }

    public function match($pathinfo)
    {
        @trigger_error(
            __METHOD__.'() is deprecated since version 1.3 and will be removed in 2.0. Use matchRequest() instead.',
            E_USER_DEPRECATED
        );
    }

    public function supports($name)
    {
        return 'maquette' === $name;
    }

    /**
     * @see \Symfony\Cmf\Component\Routing\VersatileGeneratorInterface::getRouteDebugMessage()
     */
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
