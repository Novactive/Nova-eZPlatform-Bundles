<?php
/**
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 */

namespace Novactive\EzMenuManager\Service;

use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\Cache\CacheItem;

class MenuCacheService
{
    /** @var TagAwareAdapterInterface */
    protected $cache;

    /**
     * @var SiteAccess
     */
    protected $siteAccess;

    /**
     * MenuCacheService constructor.
     *
     * @param TagAwareAdapterInterface $cache
     */
    public function __construct(TagAwareAdapterInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param SiteAccess $siteAccess
     */
    public function setSiteAccess($siteAccess)
    {
        $this->siteAccess = $siteAccess;
    }

    /**
     * @param $key
     *
     * @throws \Psr\Cache\InvalidArgumentException
     *
     * @return CacheItem
     */
    public function getItem($key)
    {
        if ($this->siteAccess instanceof SiteAccess) {
            $key .= "siteaccess_{$this->siteAccess->name}-";
        }

        return $this->cache->getItem($key);
    }
}
