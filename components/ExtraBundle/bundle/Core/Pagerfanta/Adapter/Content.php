<?php

/**
 * NovaeZExtraBundle ConentListTree Adapter.
 *
 * @package   Novactive\Bundle\eZExtraBundle
 *
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZExtraBundle\Core\Pagerfanta\Adapter;

use eZ\Publish\Core\Pagination\Pagerfanta\LocationSearchAdapter;
use Novactive\Bundle\eZExtraBundle\Core\Helper\eZ\Result;
use Novactive\Bundle\eZExtraBundle\Core\Helper\eZ\WrapperFactory;

final class Content extends LocationSearchAdapter
{
    /**
     * @var WrapperFactory
     */
    protected $wrapperFactory;

    public function setWrapperFactory(WrapperFactory $factory): void
    {
        $this->wrapperFactory = $factory;
    }

    protected function wrapResults($results, int $limit): Result
    {
        $contentResults = new Result();
        $contentResults->setResultTotalCount(count($results));
        $contentResults->setResultLimit($limit);
        foreach ($results as $hit) {
            $contentResults->addResult($this->wrapperFactory->createByLocation($hit));
        }

        return $contentResults;
    }

    public function getSlice(int $offset, int $length)
    {
        $list = parent::getSlice($offset, $length);

        return $this->wrapResults($list, $length);
    }
}
