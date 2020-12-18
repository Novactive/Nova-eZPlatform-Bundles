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

namespace Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\CriterionVisitor\Tags;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\SPI\Persistence\Content\Type\Handler;
use Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\CriterionVisitor\Contracts\Field;
use Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\CriterionVisitor\Contracts\FieldInterface;
use Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\CriterionVisitor\Contracts\Helper;
use Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\CriterionVisitor\CriterionVisitor;

abstract class TagsVisitor implements CriterionVisitor, FieldInterface
{
    use Field;
    use Helper;

    /**
     * For tag-queries which aren't field-specific.
     *
     * @var Handler
     */
    private $contentTypeHandler;

    /**
     * Identifier of the field type that criterion can handle.
     *
     * @var string
     */
    private $fieldTypeIdentifier;

    /**
     * Name of the field type's indexed field that criterion can handle.
     *
     * @var string
     */
    private $fieldName;

    public function __construct(
        Handler $contentTypeHandler,
        string $fieldTypeIdentifier,
        string $fieldName
    ) {
        $this->contentTypeHandler = $contentTypeHandler;
        $this->fieldTypeIdentifier = $fieldTypeIdentifier;
        $this->fieldName = $fieldName;
    }

    public function getSearchFields(Criterion $criterion): array
    {
        if (null !== $criterion->target) {
            return $this->fieldNameResolver->getFieldTypes(
                $criterion,
                $criterion->target,
                $this->fieldTypeIdentifier,
                $this->fieldName
            );
        }

        $targetFieldTypes = [];
        foreach ($this->contentTypeHandler->getSearchableFieldMap() as $fieldDefinitions) {
            foreach ($fieldDefinitions as $fieldIdentifier => $fieldDefinition) {
                if (!isset($fieldDefinition['field_type_identifier'])) {
                    continue;
                }

                if ($fieldDefinition['field_type_identifier'] !== $this->fieldTypeIdentifier) {
                    continue;
                }

                $fieldTypes = $this->fieldNameResolver->getFieldTypes(
                    $criterion,
                    $fieldIdentifier,
                    $this->fieldTypeIdentifier,
                    $this->fieldName
                );

                $targetFieldTypes[] = $fieldTypes;
            }
        }

        return array_merge(...$targetFieldTypes);
    }

    public function visit(CriterionVisitor $dispatcher, Criterion $criterion, string $additionalOperators = ''): string
    {
        if ($this instanceof TagKeywordVisitor && Criterion\Operator::LIKE === $criterion->operator) {
            throw new InvalidArgumentException(
                '$criterion->operator',
                "'{$criterion->operator}' operator is not acceptable for TagKeyword Criterion"
            );
        }

        $criterion->value = (array) $criterion->value;
        $searchFields = $this->getSearchFields($criterion);

        if (0 === \count($searchFields)) {
            throw new InvalidArgumentException(
                '$criterion->target',
                "No searchable fields found for the given criterion target '{$criterion->target}'."
            );
        }

        $queries = [];
        foreach ($searchFields as $name => $fieldType) {
            foreach ($criterion->value as $value) {
                $preparedValue = $this->escapeQuote(
                    $this->toString(
                        $this->mapSearchFieldValue($value, $fieldType)
                    ),
                    true
                );
                $comparison = $this instanceof TagIdVisitor ? '='.$preparedValue : ':"'.$preparedValue.'"';
                $queries[] = $additionalOperators.$name.$comparison;
            }
        }

        return '('.implode('NOT ' === $additionalOperators ? ' AND ' : ' OR ', $queries).')';
    }
}
