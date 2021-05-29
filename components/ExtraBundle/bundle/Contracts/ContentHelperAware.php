<?php

/**
 * NovaeZExtraBundle ContentHelperAware.
 *
 * @package   Novactive\Bundle\eZExtraBundle
 *
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZExtraBundle\Contracts;

use Novactive\Bundle\eZExtraBundle\Core\Helper\eZ\Content as BaseContentHelper;

trait ContentHelperAware
{
    protected BaseContentHelper $contentHelper;

    /**
     * @required
     */
    public function setContentHelper(BaseContentHelper $contentHelper): void
    {
        $this->contentHelper = $contentHelper;
    }
}
