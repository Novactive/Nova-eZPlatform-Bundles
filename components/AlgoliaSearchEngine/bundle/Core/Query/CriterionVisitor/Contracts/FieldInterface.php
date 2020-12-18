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
use eZ\Publish\SPI\Search\FieldType;

interface FieldInterface
{
    public function getSearchFields(Criterion $criterion): array;

    public function mapSearchFieldValue($value, FieldType $searchFieldType = null);
}
