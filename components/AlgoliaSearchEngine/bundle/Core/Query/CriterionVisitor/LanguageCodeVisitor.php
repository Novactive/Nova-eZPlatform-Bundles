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

final class LanguageCodeVisitor implements CriterionVisitor
{
    public function supports(Criterion $criterion): bool
    {
        return $criterion instanceof Criterion\LanguageCode &&
               \in_array(
                   $criterion->operator,
                   [
                       Criterion\Operator::EQ,
                       Criterion\Operator::IN,
                   ],
                   true
               );
    }

    public function visit(CriterionVisitor $dispatcher, Criterion $criterion, string $additionalOperators = ''): string
    {
        $languageCodeExpressions = array_map(
            static function ($value) use ($additionalOperators) {
                return $additionalOperators.'content_language_codes_ms:"'.$value.'"';
            },
            $criterion->value
        );

        /** @var Criterion\LanguageCode $criterion */
        if ($criterion->matchAlwaysAvailable) {
            $languageCodeExpressions[] = 'content_always_available_b:true';
        }

        return '('.implode('NOT ' === $additionalOperators ? ' AND ' : ' OR ', $languageCodeExpressions).')';
    }
}
