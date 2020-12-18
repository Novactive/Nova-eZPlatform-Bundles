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

namespace Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\CriterionVisitor\Contracts;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;

trait CommonVisitor
{
    private function visitValues(array $values, string $comparison, string $additionalOperators): string
    {
        return '('.implode(
            'NOT ' === $additionalOperators ? ' AND ' : ' OR ',
            array_map(
                static function ($value) use ($additionalOperators, $comparison) {
                    return $additionalOperators.sprintf($comparison, $value);
                },
                $values
            )
        ).')';
    }

    private function visitWithOperators(Criterion $criterion, string $additionalOperators, string $indexField): string
    {
        if (\in_array($criterion->operator, [Criterion\Operator::EQ, Criterion\Operator::IN], true)) {
            $values = [];
            foreach ($criterion->value as $value) {
                $values[] = $additionalOperators.$indexField.'='.$value;
            }

            return '('.implode('NOT ' === $additionalOperators ? ' AND ' : ' OR ', $values).')';
        }

        if (Criterion\Operator::BETWEEN === $criterion->operator) {
            if (2 !== \count($criterion->value)) {
                throw new InvalidArgumentException(
                    'value',
                    "Unsupported number of values for {$criterion->operator} operator"
                );
            }

            return $additionalOperators.$indexField.':'.$criterion->value[0].' TO '.$criterion->value[1];
        }

        return $additionalOperators.$indexField.' '.$criterion->operator.' '.$criterion->value[0];
    }
}
