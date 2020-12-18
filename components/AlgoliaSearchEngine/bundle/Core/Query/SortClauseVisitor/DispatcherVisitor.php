<?php

/**
 * Nova eZ Algolia Search Engine.
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @licence   "SEE FULL LICENSE OPTIONS IN LICENSE.md"
 *            Nova eZ Algolia Search Engine is tri-licensed, meaning you must choose one of three licenses to use:
 *                - Commercial License: a paid license, meant for commercial use. The default option for most users.
 *                - Creative Commons Non-Commercial No-Derivatives: meant for trial and non-commercial use.
 *                - GPLv3 License: meant for open-source projects.
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\SortClauseVisitor;

use eZ\Publish\API\Repository\Exceptions\NotImplementedException;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;

final class DispatcherVisitor implements SortClauseVisitor
{
    /**
     * @var iterable
     */
    private $visitors;

    public function __construct(iterable $visitors = [])
    {
        $this->visitors = $visitors;
    }

    public function supports(SortClause $sortClause): bool
    {
        return null !== $this->findVisitor($sortClause);
    }

    public function visit(SortClauseVisitor $dispatcher, SortClause $sortClause): string
    {
        $visitor = $this->findVisitor($sortClause);
        if (null === $visitor) {
            throw new NotImplementedException('No visitor available for: '.\get_class($sortClause));
        }

        return $visitor->visit($dispatcher, $sortClause);
    }

    private function findVisitor(SortClause $sortClause): ?SortClauseVisitor
    {
        foreach ($this->visitors as $visitor) {
            if ($visitor->supports($sortClause)) {
                return $visitor;
            }
        }

        return null;
    }
}
