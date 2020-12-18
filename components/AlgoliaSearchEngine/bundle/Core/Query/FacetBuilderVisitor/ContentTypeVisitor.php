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

namespace Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\FacetBuilderVisitor;

use eZ\Publish\API\Repository\Values\Content\Query\FacetBuilder;
use eZ\Publish\API\Repository\Values\Content\Query\FacetBuilder\ContentTypeFacetBuilder;

final class ContentTypeVisitor extends AbstractTermsVisitor
{
    public const FACET_ATTRIBUTE = 'content_type_name_s';

    public function supports(FacetBuilder $builder): bool
    {
        return $builder instanceof ContentTypeFacetBuilder;
    }

    protected function getTargetField(FacetBuilder $builder): string
    {
        return self::FACET_ATTRIBUTE;
    }
}
