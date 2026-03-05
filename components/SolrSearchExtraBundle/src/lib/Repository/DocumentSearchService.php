<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Repository;

use Ibexa\Contracts\Core\Repository\PermissionCriterionResolver;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Location as LocationCriterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalOperator;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Location as LocationSortClause;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Novactive\EzSolrSearchExtra\Query\DocumentQuery;
use Novactive\EzSolrSearchExtra\Search\ExtendedSearchHandler;
use Novactive\EzSolrSearchExtra\Search\ExtendedSearchResult;

class DocumentSearchService implements DocumentSearchServiceInterface
{
    protected PermissionCriterionResolver $permissionCriterionResolver;
    protected ExtendedSearchHandler $searchHandler;

    public function __construct(
        PermissionCriterionResolver $permissionCriterionResolver,
        ExtendedSearchHandler $searchHandler
    ) {
        $this->searchHandler = $searchHandler;
        $this->permissionCriterionResolver = $permissionCriterionResolver;
    }

    public function findDocument(
        DocumentQuery $query,
        array $languageFilter = [],
        bool $filterOnUserPermissions = true
    ): ExtendedSearchResult {
        if (!is_int($query->offset)) {
            throw new InvalidArgumentType(
                '$query->offset',
                'integer',
                $query->offset
            );
        }

        if (!is_int($query->limit)) {
            throw new InvalidArgumentType(
                '$query->limit',
                'integer',
                $query->limit
            );
        }

        $query = clone $query;
        $query->filter = $query->filter ?: new Criterion\MatchAll();

        $this->validateContentCriteria([$query->query], '$query');
        $this->validateContentCriteria([$query->filter], '$query');
        $this->validateContentSortClauses($query);

        if ($filterOnUserPermissions && !$this->addPermissionsCriterion($query->filter)) {
            return new ExtendedSearchResult(['time' => 0, 'totalCount' => 0]);
        }

        return $this->searchHandler->findDocument($query, $languageFilter);
    }

    public function purgeDocumentsFromIndex(): void
    {
        $this->searchHandler->purgeDocumentsFromIndex();
    }

    /**
     * Checks that $criteria does not contain Location criterions.
     *
     * @param Criterion[] $criteria
     * @param string      $argumentName
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    protected function validateContentCriteria(array $criteria, $argumentName)
    {
        foreach ($criteria as $criterion) {
            if ($criterion instanceof LocationCriterion) {
                throw new InvalidArgumentException(
                    $argumentName,
                    'Location Criteria cannot be used in Content search'
                );
            }
            if ($criterion instanceof LogicalOperator) {
                $this->validateContentCriteria($criterion->criteria, $argumentName);
            }
        }
    }

    /**
     * Checks that $query does not contain Location sort clauses.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    protected function validateContentSortClauses(Query $query)
    {
        foreach ($query->sortClauses as $sortClause) {
            if ($sortClause instanceof LocationSortClause) {
                throw new InvalidArgumentException('$query', 'Location Sort Clauses cannot be used in Content search');
            }
        }
    }

    /**
     * Adds content, read Permission criteria if needed and return false if no access at all.
     *
     * @uses \Ibexa\Contracts\Core\Repository\PermissionCriterionResolver::getPermissionsCriterion()
     */
    protected function addPermissionsCriterion(Criterion &$criterion): bool
    {
        $permissionCriterion = $this->permissionCriterionResolver->getPermissionsCriterion('content', 'read');
        if (true === $permissionCriterion || false === $permissionCriterion) {
            return $permissionCriterion;
        }

        // Merge with original $criterion
        if ($criterion instanceof LogicalAnd) {
            $criterion->criteria[] = $permissionCriterion;
        } else {
            $criterion = new LogicalAnd(
                [
                    $criterion,
                    $permissionCriterion,
                ]
            );
        }

        return true;
    }
}
