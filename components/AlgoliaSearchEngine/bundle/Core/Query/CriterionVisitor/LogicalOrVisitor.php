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

final class LogicalOrVisitor implements CriterionVisitor
{
    public function supports(Criterion $criterion): bool
    {
        return $criterion instanceof Criterion\LogicalOr;
    }

    public function visit(CriterionVisitor $dispatcher, Criterion $criterion, string $additionalOperators = ''): string
    {
        /** @var Criterion\LogicalOr $criterion */
        if (empty($criterion->criteria)) {
            throw new InvalidArgumentException('criterion', 'Invalid aggregation in LogicalOr criterion.');
        }

        $subCriteria = array_map(
            static function ($value) use ($dispatcher, $additionalOperators) {
                if ($value instanceof Criterion\LogicalOr || $value instanceof Criterion\LogicalAnd) {
                    // the reference for checking out the way to manage that:
                    // https://www.algolia.com/doc/api-reference/api-parameters/filters/#boolean-operators
                    $docRef = 'Check out the reference of Algolia boolean operators: ';
                    $docRef .= 'https://www.algolia.com/doc/api-reference/api-parameters/filters/#boolean-operators';
                    throw new InvalidArgumentException(
                        'criterion',
                        'AND/OR operator cannot be inside LogicalOr criterion. '.$docRef
                    );
                }
                if ($value instanceof Criterion\FullText) {
                    throw new InvalidArgumentException(
                        'criterion',
                        'FullText Criterion cannot be inside LogicalOr operator '.
                        "because it's moved to the query string of the Algolia request which is performed anyway."
                    );
                }

                return $dispatcher->visit($dispatcher, $value, $additionalOperators);
            },
            $criterion->criteria
        );

        if (1 === \count($subCriteria)) {
            return reset($subCriteria);
        }

        return '('.implode('NOT ' === $additionalOperators ? ' AND ' : ' OR ', $subCriteria).')';
    }
}
