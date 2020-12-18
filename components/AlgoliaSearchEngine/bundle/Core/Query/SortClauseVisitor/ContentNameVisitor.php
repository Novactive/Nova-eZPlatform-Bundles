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

use eZ\Publish\API\Repository\Values\Content\Query\SortClause;

final class ContentNameVisitor extends AbstractSortClauseVisitor
{
    public function supports(SortClause $sortClause): bool
    {
        return $sortClause instanceof SortClause\ContentName;
    }

    public function visit(SortClauseVisitor $visitor, SortClause $sortClause): string
    {
        return 'sort_by_content_name_s_'.$this->getDirection($sortClause);
    }
}
