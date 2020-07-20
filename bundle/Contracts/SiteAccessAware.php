<?php

/**
 * Nova eZ Accelerator.
 *
 * @package   Novactive\Bundle\eZAccelerator
 *
 * @author    Novactive <dir.tech@novactive.com>
 * @author    Sébastien Morel (Plopix) <morel.seb@gmail.com>
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZAccelerator/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZAccelerator\Contracts;

use eZ\Publish\Core\MVC\Symfony\SiteAccess;

trait SiteAccessAware
{
    /**
     * @var SiteAccess
     */
    private $siteAccess;

    public function setSiteAccess(SiteAccess $siteAccess = null): void
    {
        $this->siteAccess = $siteAccess;
    }

    public function getSiteAccessName(): string
    {
        return null !== $this->siteAccess ? $this->siteAccess->name : 'default';
    }

    public function getSiteAccess(): ?SiteAccess
    {
        return $this->siteAccess;
    }
}
