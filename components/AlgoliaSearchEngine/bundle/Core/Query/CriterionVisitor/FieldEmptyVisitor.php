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
use eZ\Publish\SPI\Search\FieldType\BooleanField;
use EzSystems\EzPlatformSolrSearchEngine\FieldMapper\ContentTranslationFieldMapper\ContentDocumentEmptyFields;
use Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\CriterionVisitor\Contracts\Field;
use Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\CriterionVisitor\Contracts\FieldInterface;

final class FieldEmptyVisitor implements CriterionVisitor, FieldInterface
{
    use Field;

    public function supports(Criterion $criterion): bool
    {
        return $criterion instanceof Criterion\IsFieldEmpty;
    }

    public function visit(CriterionVisitor $dispatcher, Criterion $criterion, string $additionalOperators = ''): string
    {
        $searchFields = $this->getSearchFields($criterion);

        if (empty($searchFields)) {
            throw new InvalidArgumentException(
                'fieldDefinitionIdentifier',
                "No searchable fields found for the given Criterion target '{$criterion->target}'."
            );
        }

        $criterion->value = (array) $criterion->value;
        $queries = [];

        foreach ($criterion->value as $value) {
            $name = $this->fieldNameGenerator->getTypedName(
                $this->fieldNameGenerator->getName(
                    ContentDocumentEmptyFields::IS_EMPTY_NAME,
                    $criterion->target
                ),
                new BooleanField()
            );
            $queries[] = $additionalOperators.$name.':'.($value ? 'true' : 'false');
        }

        return '('.implode('NOT ' === $additionalOperators ? ' AND ' : ' OR ', $queries).')';
    }
}
