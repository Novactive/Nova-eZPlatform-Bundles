<?php
/**
 * NovaeZExtraBundle Search
 *
 * @package   Novactive\Bundle\eZExtraBundle
 * @author    Novactive <dir.tech@novactive.com>
 * @copyright 2015 Novactive
 * @license   https://github.com/Novactive/NovaeZSEOBundle/blob/master/LICENSE MIT Licence
 */
namespace Novactive\Bundle\eZExtraBundle\Core\Helper\eZ;

use eZ\Publish\API\Repository\Repository as RepositoryInterface;
use eZ\Publish\Core\MVC\Legacy\Kernel;
use eZFunctionHandler;
use Novactive\Bundle\eZExtraBundle\Core\Helper\Search\Structure;
use eZFindResultNode;

/**
 * Class Search
 */
class Search
{
    /**
     * Repository eZ Publish
     *
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * Legacy Kernel Closure
     *
     * @var \Closure
     */
    protected $legacyKernel;

    /**
     * Constructor
     *
     * @param RepositoryInterface $repository
     * @param \Closure            $kernel
     */
    public function __construct( RepositoryInterface $repository, \Closure $kernel )
    {
        $this->repository   = $repository;
        $this->legacyKernel = $kernel;
    }

    /**
     * Search function
     *
     * @param Structure $structure
     *
     * @return Result
     */
    public function search( Structure $structure )
    {
        /** @var \Closure $legacyKernelClosure */
        $legacyKernelClosure = $this->legacyKernel;
        $searchResults       = $legacyKernelClosure()->runCallback(
            function () use ( $structure )
            {
                $results = [];
                $resultsLegacy = eZFunctionHandler::execute( 'ezfind', 'search', $structure->geteZLegacyFindQuery() );
                $results['results'] = $resultsLegacy['SearchResult'];
                $extraAttributes = $resultsLegacy['SearchExtras']->attributes();
                // we need to pre load the extra attribute, cause they are lazy loaded.. and on the Twig stack we won't
                // be able to load the data
                foreach ( $extraAttributes as $attr )
                {
                    $results['extras'][$attr] = $resultsLegacy['SearchExtras']->attribute( $attr );
                }
                $results['count'] = $resultsLegacy['SearchCount'];
                return $results;
            }
        );
        $contentResults      = new Result();
        $contentResults->setResultTotalCount( $searchResults['count'] );
        $contentResults->setResultLimit( $structure->getLimit() );
        $contentResults->setExtras( $searchResults['extras'] );
        $searchResults = $searchResults['results'];
        $extra = null;
        foreach ( $searchResults as $result )
        {
            if ( $result instanceof eZFindResultNode ) {
                $contentId = $result->ContentObject->ID;
                $locationId = $result->NodeID;
            }
            else
            {
                $contentId = $result['id'];
                $locationId = $result['main_node_id'];
                $extra = $result;
            }

            $contentResults->addResult(
                new Wrapper(
                    $this->repository->getContentService()->loadContent( $contentId ),
                    $this->repository->getLocationService()->loadLocation( $locationId ),
                    $extra
                )
            );

        }

        return $contentResults;
    }

}
