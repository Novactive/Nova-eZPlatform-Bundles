<?php

/**
 * NovaeZExtraBundle WrapperFactoryAware.
 *
 * @package   Novactive\Bundle\eZExtraBundle
 *
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZExtraBundle\Contracts;

use Novactive\Bundle\eZExtraBundle\Core\Helper\eZ\WrapperFactory;

trait WrapperFactoryAware
{
    /**
     * @var WrapperFactory
     */
    protected $wrapperFactory;

    /**
     * @required
     */
    public function setWrapperFactory(WrapperFactory $wrapperFactory): void
    {
        $this->wrapperFactory = $wrapperFactory;
    }
}
