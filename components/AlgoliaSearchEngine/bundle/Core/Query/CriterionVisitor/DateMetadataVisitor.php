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
use Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\CriterionVisitor\Contracts\CommonVisitor;

final class DateMetadataVisitor implements CriterionVisitor
{
    use CommonVisitor;

    public function supports(Criterion $criterion): bool
    {
        return $criterion instanceof Criterion\DateMetadata &&
               \in_array(
                   $criterion->operator,
                   [
                       Criterion\Operator::EQ,
                       Criterion\Operator::IN,
                       Criterion\Operator::LT,
                       Criterion\Operator::LTE,
                       Criterion\Operator::GT,
                       Criterion\Operator::GTE,
                       Criterion\Operator::BETWEEN,
                   ],
                   true
               );
    }

    public function visit(CriterionVisitor $dispatcher, Criterion $criterion, string $additionalOperators = ''): string
    {
        return $this->visitWithOperators($criterion, $additionalOperators, $this->getTargetField($criterion));
    }

    private function getTargetField(Criterion $criterion): string
    {
        switch ($criterion->target) {
            case Criterion\DateMetadata::CREATED:
                return 'content_publication_date_timestamp_i';
            case Criterion\DateMetadata::MODIFIED:
                return 'content_modification_date_timestamp_i';
            default:
                throw new InvalidArgumentException(
                    'target',
                    "Unsupported DateMetadata criterion target {$criterion->target}"
                );
        }
    }
}
