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

use eZ\Publish\API\Repository\Exceptions\NotImplementedException;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\CriterionVisitor\Contracts\CommonVisitor;

final class UserMetadataVisitor implements CriterionVisitor
{
    use CommonVisitor;

    public function supports(Criterion $criterion): bool
    {
        return $criterion instanceof Criterion\UserMetadata &&
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
        return $this->visitWithOperators($criterion, $additionalOperators, $this->getTargetField($criterion));
    }

    private function getTargetField(Criterion $criterion): string
    {
        switch ($criterion->target) {
            case Criterion\UserMetadata::MODIFIER:
                $fieldName = 'content_version_creator_user_id_i';
                break;
            case Criterion\UserMetadata::OWNER:
                $fieldName = 'content_owner_user_id_i';
                break;
            case Criterion\UserMetadata::GROUP:
                $fieldName = 'content_owner_user_group_id_mi';
                break;
            default:
                throw new NotImplementedException(
                    'No visitor available for target: '.$criterion->target.' with operator: '.$criterion->operator
                );
        }

        return $fieldName;
    }
}
