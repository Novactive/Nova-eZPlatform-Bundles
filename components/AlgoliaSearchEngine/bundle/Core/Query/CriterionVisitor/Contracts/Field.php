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
use eZ\Publish\Core\Search\Common\FieldNameGenerator;
use eZ\Publish\Core\Search\Common\FieldNameResolver;
use eZ\Publish\Core\Search\Common\FieldValueMapper;
use eZ\Publish\SPI\Search\Field as SearchField;
use eZ\Publish\SPI\Search\FieldType;

trait Field
{
    /**
     * @var FieldNameResolver
     */
    private $fieldNameResolver;

    /**
     * @var FieldValueMapper
     */
    private $fieldValueMapper;

    /**
     * @var FieldNameGenerator
     */
    private $fieldNameGenerator;

    public function setServices(
        FieldNameResolver $fieldNameResolver,
        FieldValueMapper $fieldValueMapper,
        FieldNameGenerator $fieldNameGenerator
    ): void {
        $this->fieldNameResolver = $fieldNameResolver;
        $this->fieldValueMapper = $fieldValueMapper;
        $this->fieldNameGenerator = $fieldNameGenerator;
    }

    public function getSearchFields(Criterion $criterion): array
    {
        return $this->fieldNameResolver->getFieldTypes(
            $criterion,
            $criterion->target
        );
    }

    public function mapSearchFieldValue($value, FieldType $searchFieldType = null)
    {
        if (null === $searchFieldType) {
            return $value;
        }

        $searchField = new SearchField('field', $value, $searchFieldType);
        $value = (array) $this->fieldValueMapper->map($searchField);

        return current($value);
    }
}
