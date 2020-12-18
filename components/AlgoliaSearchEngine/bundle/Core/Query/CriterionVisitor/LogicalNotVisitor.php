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

namespace Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\CriterionVisitor;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;

final class LogicalNotVisitor implements CriterionVisitor
{
    public function supports(Criterion $criterion): bool
    {
        return $criterion instanceof Criterion\LogicalNot;
    }

    public function visit(CriterionVisitor $dispatcher, Criterion $criterion, string $additionalOperators = ''): string
    {
        /** @var Criterion\LogicalNot $criterion */
        if (1 !== \count($criterion->criteria)) {
            throw new InvalidArgumentException('criterion', 'Invalid aggregation in LogicalNot criterion.');
        }

        if ($criterion->criteria[0] instanceof Criterion\LogicalAnd) {
            // the reference for checking out the way to manage that:
            // https://www.algolia.com/doc/api-reference/api-parameters/filters/#boolean-operators
            $docRef = 'Check out the reference of Algolia boolean operators: ';
            $docRef .= 'https://www.algolia.com/doc/api-reference/api-parameters/filters/#boolean-operators';
            throw new InvalidArgumentException(
                'criterion',
                'AND operator cannot be inside LogicalNot criterion. '.$docRef
            );
        }

        if ($criterion->criteria[0] instanceof Criterion\FullText) {
            throw new InvalidArgumentException(
                'criterion',
                'FullText Criterion cannot be inside LogicalNot operator '.
                "because it's moved to the query string of the Algolia request which is performed anyway."
            );
        }

        return $dispatcher->visit($dispatcher, $criterion->criteria[0], 'NOT ');
    }
}
