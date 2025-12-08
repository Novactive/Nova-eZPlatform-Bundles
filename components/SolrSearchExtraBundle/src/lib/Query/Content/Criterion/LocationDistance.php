<?php

declare(strict_types=1);

namespace Novactive\EzSolrSearchExtra\Query\Content\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator\Specifications;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Value\MapLocationValue;

class LocationDistance extends Criterion
{
    /**
     * @param string        $target    FieldDefinition identifier
     * @param string        $operator  One of the supported Operator constants
     * @param float|float[] $distance  The match value in kilometers, either as an array
     *                                 or as a single value, depending on the operator
     * @param float         $latitude  Latitude of the location that distance is calculated from
     * @param float         $longitude Longitude of the location that distance is calculated from
     */
    public function __construct(string $target, string $operator, $distance, float $latitude, float $longitude)
    {
        $distanceStart = new MapLocationValue($latitude, $longitude);
        parent::__construct($target, $operator, $distance, $distanceStart);
    }

    public function getSpecifications(): array
    {
        return [
            new Specifications(Operator::IN, Specifications::FORMAT_ARRAY),
            new Specifications(Operator::EQ, Specifications::FORMAT_SINGLE),
            new Specifications(Operator::GT, Specifications::FORMAT_SINGLE),
            new Specifications(Operator::GTE, Specifications::FORMAT_SINGLE),
            new Specifications(Operator::LT, Specifications::FORMAT_SINGLE),
            new Specifications(Operator::LTE, Specifications::FORMAT_SINGLE),
            new Specifications(Operator::BETWEEN, Specifications::FORMAT_ARRAY, null, 2),
        ];
    }
}
