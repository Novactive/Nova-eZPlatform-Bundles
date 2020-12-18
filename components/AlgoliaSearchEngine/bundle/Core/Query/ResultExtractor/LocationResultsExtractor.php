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

namespace Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\ResultExtractor;

use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\SPI\Persistence\Content\Location\Handler as LocationHandler;
use Novactive\Bundle\eZAlgoliaSearchEngine\Core\Query\ResultExtractor\FacetResultExtractor\FacetResultExtractor;

final class LocationResultsExtractor extends AbstractResultsExtractor
{
    public const LOCATION_ID_FIELD = 'location_id_i';

    /** @var LocationHandler */
    private $locationHandler;

    public function __construct(
        LocationHandler $locationHandler,
        FacetResultExtractor $facetResultExtractor,
        bool $skipMissingLocations = true
    ) {
        parent::__construct($facetResultExtractor, $skipMissingLocations);

        $this->locationHandler = $locationHandler;
    }

    protected function loadValueObject(array $document): ValueObject
    {
        return $this->locationHandler->load((int) $document[self::LOCATION_ID_FIELD]);
    }

    public function getExpectedSourceFields(): array
    {
        return [
            self::MATCHED_TRANSLATION_FIELD,
            self::LOCATION_ID_FIELD,
        ];
    }
}
