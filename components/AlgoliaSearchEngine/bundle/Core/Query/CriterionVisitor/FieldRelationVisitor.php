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
use Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\CriterionVisitor\Contracts\Field;
use Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\CriterionVisitor\Contracts\FieldInterface;
use Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\CriterionVisitor\Contracts\Helper;

final class FieldRelationVisitor implements CriterionVisitor, FieldInterface
{
    use Helper;
    use Field;

    public function supports(Criterion $criterion): bool
    {
        return $criterion instanceof Criterion\FieldRelation && Criterion\Operator::IN === $criterion->operator;
    }

    public function visit(CriterionVisitor $dispatcher, Criterion $criterion, string $addOperators = ''): string
    {
        $searchFields = $this->getSearchFields($criterion);

        if (empty($searchFields)) {
            throw new InvalidArgumentException(
                'target',
                "No searchable Fields found for the provided Criterion target '{$criterion->target}'."
            );
        }

        $criterion->value = (array) $criterion->value;
        $queries = [];

        foreach ($searchFields as $name => $fieldType) {
            foreach ($criterion->value as $value) {
                $preparedValues = (array) $this->mapSearchFieldvalue($value, $fieldType);
                foreach ($preparedValues as $prepValue) {
                    $queries[] = $addOperators.$name.':"'.$this->escapeQuote($this->toString($prepValue), true).'"';
                }
            }
        }

        return '('.implode('NOT ' === $addOperators ? ' AND ' : ' OR ', $queries).')';
    }
}
