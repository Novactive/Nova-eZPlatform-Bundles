<?php
/**
 * NovaeZExtraBundle ConentListTree Adapter
 *
 * @package   Novactive\Bundle\eZExtraBundle
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZExtraBundle/blob/master/LICENSE MIT Licence
 */
namespace Novactive\Bundle\eZExtraBundle\Core\Pagerfanta\Adapter;

use eZ\Publish\Core\Pagination\Pagerfanta\LocationSearchAdapter;
use Novactive\Bundle\eZExtraBundle\Core\Helper\eZ\Result;
use Novactive\Bundle\eZExtraBundle\Core\Helper\eZ\WrapperFactory;

/**
 * Class Content
 */
class Content extends LocationSearchAdapter
{

    /**
     * The Wrapper Factory
     *
     * @var WrapperFactory
     */
    protected $wrapperFactory;

    /**
     * Set The Wrapper Factory
     *
     * @param WrapperFactory $factory
     */
    public function setWrapperFactory(WrapperFactory $factory)
    {
        $this->wrapperFactory = $factory;
    }

    /**
     * Wrap into content/location
     *
     * @param mixed   $results
     * @param integer $limit
     *
     * @return Result
     */
    protected function wrapResults($results, $limit)
    {
        $contentResults = new Result();
        $contentResults->setResultTotalCount(count($results));
        $contentResults->setResultLimit($limit);
        foreach ($results as $hit) {
            $contentResults->addResult($this->wrapperFactory->createByLocation($hit));
        }

        return $contentResults;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        $list = parent::getSlice($offset, $length);

        return $this->wrapResults($list, $length);
    }
}
