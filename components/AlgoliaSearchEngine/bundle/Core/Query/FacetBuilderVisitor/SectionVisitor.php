<?php

/**
 * Nova eZ Algolia Search Engine.
 *
 * @author    Novactive - Maxim Strukov <m.strukov@novactive.com>
 * @copyright 2020 Novactive
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\FacetBuilderVisitor;

use eZ\Publish\API\Repository\Values\Content\Query\FacetBuilder;
use eZ\Publish\API\Repository\Values\Content\Query\FacetBuilder\SectionFacetBuilder;

final class SectionVisitor extends AbstractTermsVisitor
{
    public const FACET_ATTRIBUTE = 'section_name_s';

    public function supports(FacetBuilder $builder): bool
    {
        return $builder instanceof SectionFacetBuilder;
    }

    protected function getTargetField(FacetBuilder $builder): string
    {
        return self::FACET_ATTRIBUTE;
    }
}
