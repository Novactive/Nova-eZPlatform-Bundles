<?php

/**
 * Nova eZ Algolia Search Engine.
 *
 * @author    Novactive - Maxim Strukov <m.strukov@novactive.com>
 * @copyright 2020 Novactive
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\ResultExtractor\FacetResultExtractor;

use eZ\Publish\API\Repository\Values\Content\Query\FacetBuilder;
use eZ\Publish\API\Repository\Values\Content\Query\FacetBuilder\SectionFacetBuilder;
use eZ\Publish\API\Repository\Values\Content\Search\Facet;
use eZ\Publish\API\Repository\Values\Content\Search\Facet\SectionFacet;
use Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\FacetBuilderVisitor\SectionVisitor;

final class SectionResultExtractor implements FacetResultExtractor
{
    public function supports(FacetBuilder $builder): bool
    {
        return $builder instanceof SectionFacetBuilder;
    }

    public function extract(FacetBuilder $builder, array $data): Facet
    {
        $facet = new SectionFacet();
        $facet->name = $builder->name ?? SectionVisitor::FACET_ATTRIBUTE;
        $facet->entries = $data[SectionVisitor::FACET_ATTRIBUTE];

        return $facet;
    }
}
