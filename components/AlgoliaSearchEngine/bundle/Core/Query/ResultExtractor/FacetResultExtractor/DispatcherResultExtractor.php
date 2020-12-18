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

namespace Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\ResultExtractor\FacetResultExtractor;

use eZ\Publish\API\Repository\Exceptions\NotImplementedException;
use eZ\Publish\API\Repository\Values\Content\Query\FacetBuilder;
use eZ\Publish\API\Repository\Values\Content\Search\Facet;

final class DispatcherResultExtractor implements FacetResultExtractor
{
    /**
     * @var iterable
     */
    private $extractors;

    public function __construct(iterable $extractors = [])
    {
        $this->extractors = $extractors;
    }

    public function supports(FacetBuilder $builder): bool
    {
        return null !== $this->findExtractor($builder);
    }

    public function extract(FacetBuilder $builder, array $data): Facet
    {
        $extractor = $this->findExtractor($builder);

        if (null === $extractor) {
            throw new NotImplementedException(
                'No result extractor available for: '.get_class($builder)
            );
        }

        return $extractor->extract($builder, $data);
    }

    private function findExtractor(FacetBuilder $builder): ?FacetResultExtractor
    {
        foreach ($this->extractors as $extractor) {
            if ($extractor->supports($builder)) {
                return $extractor;
            }
        }

        return null;
    }
}
